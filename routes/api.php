<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Middleware\AuthenticateWithToken;
use App\Http\Middleware\CheckApiToken;

Route::middleware([CheckApiToken::class])->group(function() {
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::post('/posts/{postId}/like', [LikeController::class, 'like']);
    Route::delete('/posts/{postId}/unlike', [LikeController::class, 'unlike']);
    Route::get('/posts/{postId}/checkIfLiked', [LikeController::class, 'checkIfLiked']);
    Route::get('/user/posts', [PostController::class, 'userPosts']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
});
// Route::middleware('auth:api')->post('/posts', [PostController::class, 'store']);  
// Route::middleware('auth:api')->put('/posts/{id}', [PostController::class, 'update']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware(AuthenticateWithToken::class);

Route::get('/posts', [PostController::class, 'index']);
// Route::post('/posts', [PostController::class, 'store']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// Route::put('/posts/{id}', [PostController::class, 'update']);
// Route::delete('/posts/{id}', [PostController::class, 'destroy']);
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');
