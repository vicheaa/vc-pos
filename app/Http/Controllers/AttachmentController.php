<?php

namespace App\Http\Controllers;

use App\Contracts\AttacementServiceInterface;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{

    public function __construct(
        protected AttacementServiceInterface $attachmentService
    ) {
        // parent::__construct();
    }

    public function index()
    {
        return ApiResponse::success(message: 'Attachments fetched successfully', data: Attachment::get());
    }

    /**
     * Upload a single file
     *
     * @param \App\Http\Requests\StoreAttachmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(StoreAttachmentRequest $request)
    {
        $file       = $request->validated('file');
        $attachTo   = $request->validated('attach_to');

        try {
            $result = $this->attachmentService->uploadFile($file, $attachTo);
            return ApiResponse::success(message: 'File uploaded successfully', data: $result);
        } catch (\Exception $e) {
            return ApiResponse::error(message: 'Failed to upload file', errors: $e->getMessage(), code: 500);
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
