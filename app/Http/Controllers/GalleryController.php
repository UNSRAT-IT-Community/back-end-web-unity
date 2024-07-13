<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Interfaces\GalleryRepositoryInterface;
use App\Models\Gallery;


class GalleryController extends Controller
{
    protected $galleryRepository;

    public function __construct(GalleryRepositoryInterface $galleryRepository)
    {
        $this->middleware('pengurus');
        $this->galleryRepository = $galleryRepository;
    }

    public function destroy(Gallery $gallery)
    {
        try {
            if ($this->galleryRepository->deleteGallery($gallery)) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Success',
                    'data' => $gallery
                ]);
            } else {
                return $this->sendForbiddenResponse();
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed',
                'error' => $e->getMessage()
            ]);
        }
    }
}
