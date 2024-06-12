<?php

namespace App\Services;

use App\Entities\Post;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PostService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function index(): JsonResponse
    {
        $message = 'К сожалению статей нет';
        $posts = $this->em->getRepository(
            Post::class)->findBy([], ['created_at' => 'DESC']);
        if ($posts) {
            return response()->json($posts);
        }
        return response()->json(['message' => $message]);
    }
    public function store($request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $post = new Post();
        $data = $request->json()->all();
        $post->title = $data['title'];
        $post->description = $data['description'];

        $this->em->persist($post);
        $this->em->flush();

        return response()->json($post);
    }
}
