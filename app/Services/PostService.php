<?php

namespace App\Services;

use App\Entities\Category;
use App\Entities\Post;
use App\Entities\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class PostService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function authorizeUserForPost($id)
    {
        $message_error = "'Пост №'.$id.' не найден";
        $error_auth = "Вы не являетесь автором поста";
        $post = $this->em->getRepository(Post::class)->find($id);
        if(!$post) {
            return response()->json(['message' => $message_error]);
        }
        $user = Auth::user();
        if (!$user instanceof User || $post->getUser()->getId() !== $user->getId()) {
            return response()->json(['message' => $error_auth]);
        }
        return $post;
    }

    public function index(): JsonResponse
    {
        $posts = $this->em->getRepository(
            Post::class)->findBy([], ['id' => 'DESC']);
        return response()->json($posts);
    }




    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json(['error' => 'Пользователь не авторизован']);
        }

        $data = $request->all();
        $post = new Post();
        $post->setUser($user);
        $post->setTitle($data['title']);
        $post->setDescription($data['description']);
        $post->setCreatedAt(new DateTime);
        $post->setUpdatedAt(new DateTime);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('post_images', 'public');
            $post->setImage($imagePath);
        }

        foreach ($data['category_ids'] as $categoryId) {
            $category = $this->em->getRepository(Category::class)->find($categoryId);
            if ($category) {
                $post->getCategories()->add($category);
            }
        }

        $this->em->persist($post);
        $this->em->flush();

        return response()->json($post);
    }

    public function update($id, Request $request): JsonResponse
    {
        $data = $request->all();
        $authorizedPost = $this->authorizeUserForPost($id);
        if ($authorizedPost instanceof JsonResponse) {
            return $authorizedPost;
        }

        if (isset($data['title'])) {
            $authorizedPost->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $authorizedPost->setDescription($data['description']);
        }
        if (isset($data['category_ids'])) {
            $categoryIds = $request->input('category_ids');
            $authorizedPost->getCategories()->clear();

            foreach ($categoryIds as $categoryId) {
                $category = $this->em->getRepository(Category::class)->find($categoryId);
                if ($category) {
                    $authorizedPost->getCategories()->add($category);
                }
            }
        }

        if ($request->hasFile('image')) {
            if ($authorizedPost->getImage()) {
                Storage::disk('public')->delete($authorizedPost->getImage());
            }
            $imagePath = $request->file('image')->store('post_images', 'public');
            $authorizedPost->setImage($imagePath);
        }

        $authorizedPost->setUpdatedAt(new DateTime);
        $this->em->persist($authorizedPost);
        $this->em->flush();

        return response()->json($authorizedPost);
    }


    public function show($id): JsonResponse {
        $message_error = 'Пост'.$id.' не найден';
        $post = $this->em->getRepository(Post::class)->find($id);
        if(!$post) {
            return response()->json(['message'=>$message_error]);
        }
        return response()->json($post);
    }



    public function destroy($id): JsonResponse {
        $message_delete = 'Пост №'.$id.' успешно удален';
        $authorizedPost = $this->authorizeUserForPost($id);
        if ($authorizedPost instanceof JsonResponse) {
            return $authorizedPost;
        }
        $authorizedPost->getCategories()->clear();
        $this->em->remove($authorizedPost);
        $this->em->flush();
        return response()->json(['message' => $message_delete]);
    }
}
