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

        // Store file in storage/app/public/{attachTo}/ directory
        $filePath       = $file->storeAs($attachTo, $fileName, 'public');

        // Save to database
        $attachment = Attachment::create([
            'file_name' => $originalName,
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        // Return file information
        return [
            'id'            => $attachment->id,
            'original_name' => $originalName,
            'file_name'     => $fileName,
            'file_path'     => $filePath,
            'file_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
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
}
