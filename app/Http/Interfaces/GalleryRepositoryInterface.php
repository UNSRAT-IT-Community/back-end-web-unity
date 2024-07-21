<?php

namespace App\Http\Interfaces;

use App\Models\Gallery;
interface GalleryRepositoryInterface
{
    public function getAllGallery();
    public function createGallery(array $data);
    public function getGalleryById($id);
    public function updateGallery($id, $data); 
    public function deleteGallery(Gallery $gallery);
}