<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\ValidationController;
use App\Models\Role;
use App\Models\User;
use App\Models\Division;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('authorization')->group(function () {
    Route::get(
        '/test-api',
        function () {
            return response()->json([
                'status' => 200,
                'message' => 'Welcome to the API',
                'data' => null
            ]);
        }
    );

    Route::middleware('anggota')->group(function () {
        Route::get('/anggota', function () {
            return response()->json(['message' => 'Selamat Datang Anggota', 'user' => $GLOBALS['USER_DATA']->name]);
        });
    });

    Route::middleware('pengurus')->group(function () {
        Route::get('/pengurus', function () {
            return response()->json(['message' => 'Selamat Datang Pengurus', 'user' => $GLOBALS['USER_DATA']->name]);
        });
    });

});

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/auth/refresh-token', [AuthController::class, 'refreshTokenLogin']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::post('/insert_role', [RoleController::class, 'insert']);
Route::post('/insert_division', [DivisionController::class, 'insert']);

Route::post('/auth/validation', [ValidationController::class, 'validateTokenService']);