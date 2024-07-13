<?php

namespace App\Http\Interfaces;

use App\Models\Gallery;

interface GalleryRepositoryInterface
{
    public function deleteGallery(Gallery $gallery);
}