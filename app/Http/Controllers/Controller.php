<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected function sendSuccessResponse($data = null, $message = 'Sukses'): JsonResponse
    {
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => $message,
            'data' => $data
        ], Response::HTTP_OK);
    }

    protected function sendNotFoundResponse($message = 'Tidak ditemukan'): JsonResponse
    {
        return response()->json([
            'status' => Response::HTTP_NOT_FOUND,
            'message' => $message,
            'data' => null
        ], Response::HTTP_NOT_FOUND);
    }

    protected function sendUnauthorizedResponse($message = 'Token tidak Valid'): JsonResponse
    {
        return response()->json([
            'status' => Response::HTTP_UNAUTHORIZED,
            'message' => $message,
            'data' => null
        ], Response::HTTP_UNAUTHORIZED);
    }

    protected function sendBadRequestResponse($message = 'Bad Request'): JsonResponse
    {
        return response()->json( [
            'status' => Response::HTTP_BAD_REQUEST,
            'message' => $message,
            'data' => null
        ], Response::HTTP_BAD_REQUEST);
    }

    protected function sendValidationErrorResponse($data, $message = 'Validasi Error'): JsonResponse
    {
        return response()->json([
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $message,
            'data' => $data
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function sendInternalServerErrorResponse(?Throwable $e, $message = 'Internal Server Error'): JsonResponse
    {
        Log::error($e->getMessage());
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => $message,
            'data' => null
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function sendConflictResponse($message = 'Conflict'): JsonResponse
    {
        return response()->json([
            'status' => Response::HTTP_CONFLICT,
            'message' => $message,
            'data' => null
        ], Response::HTTP_CONFLICT);
    }

    protected function sendForbiddenResponse($message = 'Akses Terlarang'): JsonResponse
    {
        return response()->json([
            'status' => Response::HTTP_FORBIDDEN,
            'message' => $message,
            'data' => null
        ], Response::HTTP_FORBIDDEN);
    }

}
