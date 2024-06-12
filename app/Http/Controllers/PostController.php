<?php

namespace App\Http\Controllers;

use App\Entities\Post;
use Doctrine\ORM\EntityManagerInterface;
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
}
