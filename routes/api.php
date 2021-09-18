<?php

use App\Http\Controllers\AggregateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::patch('/password', [UserController::class, 'updatePassword']);
});


Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route Not Found'
    ], 404);
});
