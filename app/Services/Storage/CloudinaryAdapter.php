<?php

namespace App\Services\Storage;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CloudinaryAdapter implements StorageAdapterInterface
{
    /**
     * Upload a file to storage.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param array $options
     * @return string
     */
    public function upload(UploadedFile $file, string $path, array $options = []): string
    {
        $folder = str_replace('/', '_', $path);
        $uploadOptions = [
            'folder' => $folder,
            'public_id' => $options['public_id'] ?? Str::random(20),
        ];

        if (isset($options['transformation'])) {
            $uploadOptions['transformation'] = $options['transformation'];
        }

        $result = Cloudinary::upload($file->getRealPath(), $uploadOptions);

        // Return the public_id which serves as the path
        return $result->getPublicId();
    }

    /**
     * Delete a file from storage.
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        try {
            Cloudinary::destroy($path);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the URL for a file.
     *
     * @param string $path
     * @return string
     */
    public function url(string $path): string
    {
        return Cloudinary::getUrl($path);
    }

    /**
     * Check if a file exists.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        try {
            // This is a workaround since Cloudinary doesn't have a direct exists method
            $url = Cloudinary::getUrl($path);
            $headers = get_headers($url);
            return (bool)stripos($headers[0], "200 OK");
        } catch (\Exception $e) {
            return false;
        }
    }
}
