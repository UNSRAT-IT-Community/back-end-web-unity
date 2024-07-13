<?php

namespace App\Http\Interfaces;

interface UpcomingEventRepositoryInterface
{
    public function getAllUpcomingEvents();
    public function getUpcomingEvent($eventId);
    public function insertUpcomingEvent($data);
    public function updateUpcomingEvent($eventId, $data);
    public function deleteUpcomingEvent($eventId);
    
}