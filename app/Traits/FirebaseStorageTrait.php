<?php

namespace App\Traits;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;
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

        $expiresAt = new \DateTime('+1 hour'); 
        $signedUrl = $this->firebaseStorage->getBucket()->object($firebasePath)->signedUrl($expiresAt);

        return $signedUrl;
    }
}