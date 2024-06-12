<?php

namespace App\Services;

use App\Entities\Post;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\JsonResponse;

class PostService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function index(): JsonResponse
    {
        $posts = $this->em->getRepository(
            Post::class)->findBy([], ['created_at' => 'DESC']);
        return response()->json($posts);
    }
    public function store($request): JsonResponse
    {
        $post = new Post();
        $data = $request->json()->all();
        $post->title = $data['title'];
        $post->description = $data['description'];

        $this->em->persist($post);
        $this->em->flush();

        return response()->json($post);
    }
}
