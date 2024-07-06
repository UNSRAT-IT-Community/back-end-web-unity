<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\AnnouncementRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    protected $announcementRepository;

    public function __construct(AnnouncementRepositoryInterface $announcementRepository)
    {
        $this->middleware('pengurus')->except(['index', 'show']);
        $this->announcementRepository = $announcementRepository;
    }

    public function index()
    {
        try {
            $announcements = $this->announcementRepository->getAllAnnouncements();
            return $this->sendSuccessResponse($announcements);
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image_url' => 'nullable|string',
            ]);

            $user_data = $GLOBALS['USER_DATA'];

            $announcement = Announcement::create([
                'title' => $request->title,
                'content' => $request->content,
                'image_url' => $request->image_url,
                'creator_id' => $user_data->id,
            ]);

            return $this->sendSuccessResponse($announcement, 'Pengumuman berhasil dibuat!');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function update(Request $request, Announcement $announcement)
    {
        try {
            $user_data = $GLOBALS['USER_DATA'];

            if ($announcement->creator_id !== $user_data->id) {
                return $this->sendForbiddenResponse();
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image_url' => 'nullable|string',
            ]);

            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'image_url' => $request->image_url,
            ];

            $announcement->update($data);

            return $this->sendSuccessResponse($announcement, 'Pengumuman berhasil diubah!');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function destroy(Announcement $announcement)
    {
        try {
            $user_data = $GLOBALS['USER_DATA'];

            if ($announcement->creator_id !== $user_data->id) {
                return $this->sendForbiddenResponse();
            }

            $announcement->delete();
            return $this->sendSuccessResponse(null, 'Pengumuman berhasil dihapus!');
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }

    public function show(Announcement $announcement)
    {
        try {
            return $this->sendSuccessResponse($announcement);
        } catch (\Exception $e) {
            return $this->sendInternalServerErrorResponse($e);
        }
    }
}
