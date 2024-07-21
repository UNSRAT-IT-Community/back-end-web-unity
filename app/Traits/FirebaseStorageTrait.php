<?php

namespace App\Traits;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FirebaseStorageTrait
{
    protected $firebaseStorage;

    public function initializeFirebaseStorage()
    {
        $this->firebaseStorage = (new Factory)
            ->withServiceAccount(base_path('firebase_credentials.json'))
            ->createStorage();
    }

    public function uploadImageToFirebase(UploadedFile $file, $folder)
    {
        if (!$file->isValid()) {
            throw new \Exception('File is not valid');
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '.' . $extension;
        $fileContent = file_get_contents($file->getRealPath());
        $firebasePath = "{$folder}/{$fileName}";

        $this->firebaseStorage->getBucket()->upload($fileContent, [
            'name' => $firebasePath
        ]);

        $expiresAt = new \DateTime('+3 year'); 
        $signedUrl = $this->firebaseStorage->getBucket()->object($firebasePath)->signedUrl($expiresAt);

        return $signedUrl;
    }

    protected function extractFilePathFromUrl($url)
    {
        $parsedUrl = parse_url($url, PHP_URL_PATH);
        $filePath = ltrim($parsedUrl, '/');

        $firebaseDomain = $this->firebaseStorage->getBucket()->name() . '/';
        $filePath = str_replace($firebaseDomain, '', $filePath);
    
        return $filePath;
    }

    public function deleteImageFromFirebase($filePath)
    {
        $bucket = $this->firebaseStorage->getBucket(); 
        $object = $bucket->object($filePath);
    
        if ($object->exists()) {
            $object->delete();
        } else {
            throw new \Exception('File does not exist');
        }
    }

    public function updateImageToFirebase(UploadedFile $file, $folder, $oldFilePath = null)
    {
        if ($oldFilePath){
            $this->deleteImageFromFirebase($oldFilePath);
        }
        return $this->uploadImageToFirebase($file, $folder);
    }
}

