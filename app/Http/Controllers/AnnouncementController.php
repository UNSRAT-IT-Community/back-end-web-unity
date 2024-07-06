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
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $announcements
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed',
                'error' => $e->getMessage()
            ]);
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

            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $announcement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, Announcement $announcement)
    {
        try {
            $user_data = $GLOBALS['USER_DATA'];

            if ($announcement->creator_id !== $user_data->id) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Forbidden',
                    'error' => 'Not Authorized'
                ], 403);
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
    
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $announcement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function destroy(Announcement $announcement)
    {
        try {
            $user_data = $GLOBALS['USER_DATA'];

            if ($announcement->creator_id !== $user_data->id) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Forbidden',
                    'error' => 'Not Authorized'
                ], 403);
            }

            $announcement->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $announcement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Announcement $announcement)
    {
        try {
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $announcement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed',
                'error' => $e->getMessage()
            ]);
        }
    }
}