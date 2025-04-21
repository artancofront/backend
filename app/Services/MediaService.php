<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class MediaService
{
    /**
     * Upload a file to a temporary directory.
     */
    public function uploadToTemp(UploadedFile $file, int $userId): string
    {
        $timestamp = now()->timestamp;
        $extension = $file->getClientOriginalExtension();
        $fileName = "{$timestamp}_{$userId}.{$extension}";
        $path = "temp_media/{$fileName}";

        $file->storeAs('temp_media', $fileName);

        return $path; // this is the relative path to the stored file
    }

    /**
     * Clean temp files older than a given number of minutes.
     */
    public function cleanOldTempFiles(int $minutes = 30): void
    {
        $threshold = now()->subMinutes($minutes);
        $files = Storage::files('temp_media');

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(Storage::lastModified($file));

            if ($lastModified->lt($threshold)) {
                Storage::delete($file);
            }
        }
    }

    public function cleanOldTempFilesIfDue(int $minutes = 5): void
    {
        $lastRun = cache()->get('media_cleanup_last_run');

        if (!$lastRun || now()->diffInMinutes($lastRun) >= $minutes) {
            $this->cleanOldTempFiles($minutes); // your actual cleanup logic
            cache()->put('media_cleanup_last_run', now());
        }
    }

    /**
     * Move a file from temp directory to final destination.
     */
    public function moveToFinal(string $tempPath, string $finalDir): string
    {
        $disk = Storage::disk('public');
        $fileName = basename($tempPath);
        $finalPath = "{$finalDir}/{$fileName}";

        // Move the file
        $disk->move($tempPath, $finalPath);

        return "storage/{$finalPath}";
    }

}
