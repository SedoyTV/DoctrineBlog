<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


Route::get('/posts', [PostController::class, 'index']);
Route::middleware('auth:api')->post('/posts', [PostController::class, 'store']);
Route::middleware('auth:api')->get('/posts/{post}', [PostController::class, 'show']);
Route::middleware('auth:api')->put('/posts/{post}', [PostController::class, 'update']);
Route::middleware('auth:api')->delete('/posts/{post}', [PostController::class, 'destroy']);

Route::get('categories', [CategoryController::class, 'index']);
Route::middleware('auth:api')->get('categories/{category}', [CategoryController::class, 'show']);
Route::middleware('auth:api')->post('categories', [CategoryController::class, 'store']);
Route::middleware('auth:api')->put('/categories/{category}', [CategoryController::class, 'update']);
Route::middleware('auth:api')->delete('/categories/{category}', [CategoryController::class, 'destroy']);


