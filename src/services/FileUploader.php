<?php

namespace App\services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file, string $fileName): string
    {
        // Check if the file is an image
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
            throw new \Exception('Uniquement les images jpeg et png sont autorisées');
        }

        // Convert the image to webp format
        $image = imagecreatefromstring(file_get_contents($file->getPathname()));
        ob_start();
        imagewebp($image);
        $webpData = ob_get_contents();
        ob_end_clean();

        // Check if the uploads and photos directories exist, if not, create them
        if (!file_exists($this->targetDirectory)) {
            mkdir($this->targetDirectory, 0644, true);
        }
        if (!file_exists($this->targetDirectory . '/photos')) {
            mkdir($this->targetDirectory . '/photos', 0644, true);
        }

        // Save the converted image to the photos directory
        $fileName = $fileName . '.webp';
        file_put_contents($this->targetDirectory . '/photos/' . $fileName, $webpData);

        return $fileName;
    }
}