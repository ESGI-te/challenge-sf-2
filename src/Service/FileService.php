<?php

namespace App\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    public function __construct() {
        $this->finder = new Finder();
    }
    public function upload(UploadedFile $file, string $directory, array $allowedExtensions) {

        $fileExtension = $file->guessExtension();

        if(!$fileExtension || !in_array($fileExtension, $allowedExtensions)) {
            throw new \Exception('Invalid file extension');
        }

        $fileOriginalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileHash = bin2hex(random_bytes(10));
        $fileName = $fileOriginalFilename . '-' . $fileHash . '.' . $fileExtension;

        $file->move($directory, $fileName);

        return $fileName;

    }

    public function get($fileName, $directory)
    {
        $this->finder->files()->in($directory)->name($fileName);

        foreach ($this->finder as $file) {
            return $file;
        }

        return null;
    }

    public function delete($fileName, $directory) {
        $file = $this->get($fileName, $directory);

        if($file) {
            throw new \Exception('File not found');
        }

        unlink($file->getPathname());
    }
}