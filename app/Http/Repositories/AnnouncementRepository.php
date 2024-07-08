<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\AnnouncementRepositoryInterface;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    protected $firebaseStorage;

    public function __construct()
    {
        $this->firebaseStorage = (new Factory)
            ->withServiceAccount(config('services.firebase.credentials'))
            ->createStorage()
            ->getBucket(config('services.firebase.storage_bucket'));
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
            $data['image_url'] = $this->uploadImage($data['image']);
            unset($data['image']);
        }
        return Announcement::create($data);
    }

    public function updateAnnouncement(Announcement $announcement, array $data)
    {
        // Hapus gambar lama jika ada gambar baru
        if (isset($data['image'])) {
            if ($announcement->image_url) {
                $oldFileName = basename(parse_url($announcement->image_url, PHP_URL_PATH));
                $this->deleteImage('announcement/' . $oldFileName);
            }

            $data['image_url'] = $this->uploadImage($data['image']);
            unset($data['image']);
        }

        $announcement->update($data);
        return $announcement;
    }

    public function uploadImage($file)
    {
        $fileName = 'announcement/' . uniqid() . '.' . $file->getClientOriginalExtension();
        $this->firebaseStorage->upload(file_get_contents($file), [
            'name' => $fileName,
        ]);

        return $this->firebaseStorage->object($fileName)->signedUrl(new \DateTime('+100 years'));
    }

    public function deleteImage($fileName)
    {
        $object = $this->firebaseStorage->object($fileName);
        if ($object->exists()) {
            $object->delete();
        }
    }

    public function deleteAnnouncement(Announcement $announcement)
    {
        if ($announcement->image_url) {
            $fileName = basename(parse_url($announcement->image_url, PHP_URL_PATH));
            $this->deleteImage('announcement/' . $fileName);
        }
        return $announcement->delete();
    }
}
