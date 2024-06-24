<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\PostService;

class PostController extends Controller
{
    public postService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(): JsonResponse
    {
        return $this->postService->index();
    }

    public function store(Request $request): JsonResponse
    {
        return $this->postService->store($request);
    }
    public function show($id): JsonResponse
    {
        return $this->postService->show($id);
    }
    public function update($id, Request $request): JsonResponse {
        return $this->postService->update($id, $request);
    }
    public function destroy($id): JsonResponse
    {
        return $this->postService->destroy($id);
    }
}
