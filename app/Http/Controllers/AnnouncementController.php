<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\AnnouncementRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user_data = $GLOBALS['USER_DATA'];

            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'image' => $request->file('image'),
                'creator_id' => $user_data->id,
            ];

            $this->announcementRepository->createAnnouncement($data);

            return $this->sendSuccessResponse(null, 'Pengumuman berhasil dibuat!', Response::HTTP_CREATED);
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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'image' => $request->file('image'),
            ];

            $updatedAnnouncement = $this->announcementRepository->updateAnnouncement($announcement, $data);

            return $this->sendSuccessResponse($updatedAnnouncement, 'Pengumuman berhasil diubah!');
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

            $this->announcementRepository->deleteAnnouncement($announcement);
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
