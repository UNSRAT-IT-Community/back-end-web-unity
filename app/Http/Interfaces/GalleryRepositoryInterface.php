<?php

namespace App\Http\Interfaces;

use App\Models\Gallery;

interface GalleryRepositoryInterface
{
    public function createGallery(array $data);
    public function deleteGallery(Gallery $gallery);
}