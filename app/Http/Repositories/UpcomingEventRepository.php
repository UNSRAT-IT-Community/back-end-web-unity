<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\UpcomingEventRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpcomingEventRepository implements UpcomingEventRepositoryInterface
{
    public function getAllUpcomingEvents()
    {
        return DB::table('upcoming_events')
            ->join('users', 'upcoming_events.creator_id', '=', 'users.id')
            ->select(
                'upcoming_events.id',
                'upcoming_events.title',
                'upcoming_events.content',
                'upcoming_events.start_time',
                'upcoming_events.end_time',
                'upcoming_events.image_url',
                'users.name as creator_name',
                'upcoming_events.created_at'
            )
            ->get();
    }
}