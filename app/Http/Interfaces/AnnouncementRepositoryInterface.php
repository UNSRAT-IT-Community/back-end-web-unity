<?php

namespace App\Http\Interfaces;

use App\Models\Announcement;

interface AnnouncementRepositoryInterface
{
    public function getAllAnnouncements();
    public function createAnnouncement(array $data);
    public function updateAnnouncement(Announcement $announcement, array $data);
    public function deleteAnnouncement(Announcement $announcement);
}
