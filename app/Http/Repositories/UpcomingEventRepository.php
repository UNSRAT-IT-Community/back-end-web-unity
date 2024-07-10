<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\UpcomingEventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpcomingEventRepository implements UpcomingEventRepositoryInterface
{
    protected function fetchDataUpcomingEvent()
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
            );
    }

    public function getAllUpcomingEvents()
    {
        return $this->fetchDataUpcomingEvent()->get();
    }

    public function getUpcomingEvent($eventId)
    {
        return $this->fetchDataUpcomingEvent()->where('upcoming_events.id', $eventId)->first();
    }

    public function insertUpcomingEvent($data)
    {
        return DB::table('upcoming_events')->insert([
            'id' => Str::uuid(),
            'title' => $data['title'],
            'content' => $data['content'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'image_url' => $data['image_url'],
            'creator_id' => $data['creator_id'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function updateUpcomingEvent($eventId, $data)
    {
        return DB::table('upcoming_events')
            ->where('id', $eventId)
            ->update([
                'title' => $data['title'],
                'content' => $data['content'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'image_url' => $data['image_url'],
                'updated_at' => now(),
            ]);
    }
    
    public function deleteUpcomingEvent($eventId)
    {
        return DB::table('upcoming_events')->where('id', $eventId)->delete();
    }
    
}
