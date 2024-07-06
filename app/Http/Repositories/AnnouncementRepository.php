<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\AnnouncementRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
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
}
