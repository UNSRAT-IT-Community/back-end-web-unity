<?php

namespace App\Http\Repositories;

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

    public function createGallery(array $data)
    {
        if (isset($data['image'])) {
            $data['photo_url'] = $this->uploadImageToFirebase($data['image'], 'gallery');
            unset($data['image']);
        }
        
        if (!isset($data['photo_url'])) {
            $data['photo_url'] = '';
        }
        
        return Gallery::create($data);
    }

    public function deleteGallery(Gallery $gallery)
    {
        if ($gallery->photo_url) {
            $oldFileName = basename(parse_url($gallery->photo_url, PHP_URL_PATH));
            $this->firebaseStorage->getBucket()->object('gallery/' . $oldFileName)->delete();
        }
        return $gallery->delete();
    }
}