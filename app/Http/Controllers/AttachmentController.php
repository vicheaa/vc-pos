<?php

namespace App\Http\Controllers;

use App\Contracts\AttacementServiceInterface;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    protected $attachmentService;

    public function __construct(AttacementServiceInterface $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    public function index()
    {
        return ApiResponse::success('Attachments fetched successfully', Attachment::get());
    }

    /**
     * Upload a single file to a specific folder
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,txt|max:10240',
            'attach_to' => 'required|string|max:255'
        ]);

        try {
            $file = $request->file('file');
            $attachTo = $request->input('attach_to');

            $result = $this->attachmentService->uploadFile($file, $attachTo);

            return ApiResponse::success('File uploaded successfully', $result);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to upload file', $e->getMessage(), 500);
        }
    }

    /**
     * Delete a file from storage
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string'
        ]);

        try {
            $filePath = $request->input('file_path');
            $deleted = $this->attachmentService->deleteFile($filePath);

            if (!$deleted) {
                return ApiResponse::error('File not found or could not be deleted', null, 404);
            }

            return ApiResponse::success('File deleted successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to delete file', $e->getMessage(), 500);
        }
    }
}
