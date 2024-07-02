<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UnexpectedValueException;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $pass): Response
    {
        if ($request->header('Authorization')) {
            try {
                $publicKey = file_get_contents(base_path('/' . env('JWT_PUBLIC_KEY')));

                $jwt = explode(' ', $request->header('Authorization'))[1];
                $claims = JWT::decode($jwt, new Key($publicKey, 'RS256'));

                if (
                    !isset($claims->data->id)
                ) return $this->sendUnauthorizedResponse();

                $GLOBALS['USER_DATA'] = $claims->data;
                return $pass($request);
            } catch (UnexpectedValueException $e) {
                return $this->sendUnauthorizedResponse();
            } catch (ExpiredException $e) {
                return $this->sendUnauthorizedResponse('Token Expired');
            } catch (Throwable $error) {
                Log::error($error->getMessage());
                return $this->sendInternalServerErrorResponse();
            }
        }

        return $this->sendUnauthorizedResponse();
    }

    private function sendUnauthorizedResponse($message = 'Invalid Token') {
        return response()->json([
            'status' => Response::HTTP_UNAUTHORIZED,
            'message' => $message,
            'data' => null
        ], Response::HTTP_UNAUTHORIZED);
    }

    private function sendInternalServerErrorResponse($message = 'Internal Server Error') {
        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => $message,
            'data' => null
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
