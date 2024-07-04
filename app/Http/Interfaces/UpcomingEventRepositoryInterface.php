<?php

namespace App\Http\Interfaces;

interface UpcomingEventRepositoryInterface
{
    public function getAllUpcomingEvents();
    public function insertUpcomingEvent($data);
}