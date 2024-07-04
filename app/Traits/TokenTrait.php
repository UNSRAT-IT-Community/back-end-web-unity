<?php

namespace App\Traits;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

trait TokenTrait
{
    /**
     * Decode the JWT token from the request.
     *
     * @param Request $request
     * @return object|null
     */
    public function decodeToken(Request $request)
    {
        $token = $request->bearerToken();
        $publicKey = file_get_contents(base_path('public_key.pem'));

        try {
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            return null;
        }
    }
}