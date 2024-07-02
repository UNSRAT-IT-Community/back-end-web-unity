<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\UpcomingEventRepositoryInterface;
use Illuminate\Http\Request;

class UpcomingEventController extends Controller
{
    protected $upcomingEventRepo;

    public function __construct(UpcomingEventRepositoryInterface $upcomingEventRepo)
    {
        $this->upcomingEventRepo = $upcomingEventRepo;
    }

    public function getAllUpcomingEvents()
    {
        try {
            $events = $this->upcomingEventRepo->getAllUpcomingEvents();
            return $this->sendSuccessResponse($events, 'Berhasil mendapatkan daftar acara mendatang');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
}