<?php

namespace App\Http\Interfaces;

interface GalleryRepositoryInterface
{
    public function getAllGallery();
    public function insertGallery($data);
    public function getGalleryById($id);
    public function deleteGallery($id);
    public function updateGallery($id, $data); 
}