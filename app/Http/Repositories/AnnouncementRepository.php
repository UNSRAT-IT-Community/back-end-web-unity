<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\AnnouncementRepositoryInterface;
use App\Models\Announcement;
use App\Traits\FirebaseStorageTrait;
use Illuminate\Support\Facades\DB;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    use FirebaseStorageTrait;

    public function __construct()
    {
        $this->initializeFirebaseStorage();
    }

    public function getAllAnnouncements()
    {
        return DB::table('announcements')
            ->join('users', 'announcements.creator_id', '=', 'users.id')
            ->select(
                'announcements.id',
                'announcements.title',
                'announcements.content',
                'announcements.image_url',
                'users.name as creator_name',
                'announcements.created_at'
            )
            ->get();
    }

    public function createAnnouncement(array $data)
    {
        if (isset($data['image'])) {
            $data['image_url'] = $this->uploadImageToFirebase($data['image'], 'announcement');
            unset($data['image']);
        }
        
        if (!isset($data['image_url'])) {
            $data['image_url'] = '';
        }
        
        return Announcement::create($data);
    }

    public function updateAnnouncement(Announcement $announcement, array $data)
    {
        if (isset($data['image'])) {
            if ($announcement->image_url) {
                $oldFileName = basename(parse_url($announcement->image_url, PHP_URL_PATH));
                $this->firebaseStorage->getBucket()->object('announcement/' . $oldFileName)->delete();
            }

            $data['image_url'] = $this->uploadImageToFirebase($data['image'], 'announcement');
            unset($data['image']);
        }

        if (!isset($data['image_url'])) {
            $data['image_url'] = $announcement->image_url;
        }

        $announcement->update($data);
        return $announcement;
    }

    public function deleteAnnouncement(Announcement $announcement)
    {
        if ($announcement->image_url) {
            $oldFileName = basename(parse_url($announcement->image_url, PHP_URL_PATH));
            $this->firebaseStorage->getBucket()->object('announcement/' . $oldFileName)->delete();
        }
        return $announcement->delete();
    }
}