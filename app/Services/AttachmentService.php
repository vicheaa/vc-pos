<?php

namespace App\Services;

use App\Contracts\AttacementServiceInterface;
use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService implements AttacementServiceInterface
{
    /**
     * Upload a single file to a specific folder and save to database
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $attachTo
     * @return array
     */
    public function uploadFile($file, string $attachTo): array
    {
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            throw new \InvalidArgumentException('Invalid file provided');
        }

        // Generate unique filename
        $originalName   = $file->getClientOriginalName();
        $extension      = $file->getClientOriginalExtension();
        $fileName       = Str::random(40) . '.' . $extension;
        $mimeType       = $file->getMimeType();

        // Store file in storage/app/public/{attachTo}/ directory
        $filePath       = $file->storeAs($attachTo, $fileName, 'public');
        $absolutePath   = Storage::disk('public')->path($filePath);

        // Check if file is an image and compress/convert to WebP
        $isImage = in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);
        
        if ($isImage) {
            $webpPath = $this->compressAndConvertToWebp($absolutePath, $absolutePath, 80);
            
            if ($webpPath) {
                // Delete original file if it was converted
                if ($absolutePath !== $webpPath && file_exists($absolutePath)) {
                    unlink($absolutePath);
                }
                
                // Update file info for WebP
                $fileName   = preg_replace('/\.[^.]+$/', '.webp', $fileName);
                $filePath   = $attachTo . '/' . $fileName;
                $mimeType   = 'image/webp';
                $fileSize   = filesize($webpPath);
            } else {
                // Compression failed, use original file size
                $fileSize = $file->getSize();
            }
        } else {
            $fileSize = $file->getSize();
        }

        // Save to database
        $attachment = Attachment::create([
            'file_name' => $originalName,
            'file_path' => $filePath,
            'file_type' => $mimeType,
            'file_size' => $fileSize,
        ]);

        // Return file information
        return [
            'id'            => $attachment->id,
            'original_name' => $originalName,
            'file_name'     => $fileName,
            'file_path'     => $filePath,
            'file_type'     => $mimeType,
            'file_size'     => $fileSize,
            'url'           => asset('storage/' . $filePath),
            'folder'        => $attachTo,
            'created_at'    => $attachment->created_at,
            'updated_at'    => $attachment->updated_at
        ];
    }

    /**
     * Delete a file from storage and database
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFile(string $filePath): bool
    {
        // Find and delete from database
        $attachment = Attachment::where('file_path', $filePath)->first();
        if ($attachment) {
            $attachment->delete();
        }

        // Delete from storage
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }

        return false;
    }

    /**
     * Compress and convert image to WebP format
     *
     * @param string $file Source file path
     * @param string $destination Destination file path (will be saved as .webp)
     * @param int $quality Quality level (0-100)
     * @return string|null Returns the destination path on success, null on failure
     */
    private function compressAndConvertToWebp(string $file, string $destination, int $quality = 80): ?string
    {
        // Get image info
        $imageInfo = @getimagesize($file);
        if ($imageInfo === false) {
            return null;
        }

        $mimeType = $imageInfo['mime'];
        $image = null;

        // Create image resource based on mime type
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($file);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($file);
                // Preserve transparency
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($file);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($file);
                break;
            default:
                return null;
        }

        if (!$image) {
            return null;
        }

        // Ensure destination has .webp extension
        $webpDestination = preg_replace('/\.[^.]+$/', '.webp', $destination);

        // Convert and save as WebP
        $result = imagewebp($image, $webpDestination, $quality);

        // Free memory
        imagedestroy($image);

        return $result ? $webpDestination : null;
    }
}

