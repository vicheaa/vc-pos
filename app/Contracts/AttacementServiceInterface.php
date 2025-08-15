<?php

namespace App\Contracts;

interface AttacementServiceInterface
{
    /**
     * Upload a single file to a specific folder and save to database
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $attachTo
     * @return array
     */
    public function uploadFile($file, string $attachTo): array;

    /**
     * Delete a file from storage and database
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFile(string $filePath): bool;
}
