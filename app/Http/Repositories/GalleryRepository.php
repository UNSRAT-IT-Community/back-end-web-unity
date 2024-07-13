<?php

namespace App\Repositories;

use App\Http\Interfaces\GalleryRepositoryInterface;
use App\Models\Gallery;
use App\Traits\TokenTrait;
use App\Traits\FirebaseStorageTrait;

class GalleryRepository implements GalleryRepositoryInterface
{
    use TokenTrait, FirebaseStorageTrait;

    public function __construct()
    {
        $this->initializeFirebaseStorage();
    }

    public function deleteGallery(Gallery $gallery)
    {
        try {
            $imageUrl = $gallery->url;
            $this->deleteImageFromStorage($imageUrl);
            $gallery->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function deleteImageFromStorage($imageUrl)
    {
        $bucket = $this->firebaseStorage->getBucket();
        $object = $bucket->object($imageUrl);

        if ($object->exists()) {
            $object->delete();
        } else {
            throw new \Exception('Image not found in storage');
        }
    }
}