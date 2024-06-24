<?php

namespace App\Services;

use App\Entities\Category;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    private function authorizeUserForPost($id)
    {
        $message_error = "'Категория №'.$id.' не найден";
        $error_auth = "Вы не являетесь автором поста";
        $category = $this->em->getRepository(Category::class)->find($id);
        if(!$category) {
            return response()->json(['message' => $message_error]);
        }
        $user = Auth::user();
        if (!$user instanceof User || $category->getUser()->getId() !== $user->getId()) {
            return response()->json(['message' => $error_auth]);
        }
        return $category;
    }

    public function index(): JsonResponse {
        $categories = $this->em->getRepository(Category::class)->findAll();
        return response()->json($categories);
    }
    public function store(Request $request): JsonResponse
    {
        $error_auth = 'Пользователь не авторизован';
        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json(['error' => $error_auth]);
        }

        $category = new Category();
        $category->setUser($user);
        $category->setTitle($request->input('title'));
        $this->em->persist($category);
        $this->em->flush();
        return response()->json($category);
    }

    public function show($id): JsonResponse
    {
        $category = $this->em->getRepository(Category::class)->find($id);
        return response()->json($category);
    }

    public function update($id): JsonResponse
    {
        $authorizedPost = $this->authorizeUserForPost($id);
        if ($authorizedPost instanceof JsonResponse) {
            return $authorizedPost;
        }
        $authorizedPost = $this->em->getRepository(Category::class)->find($id);
        $authorizedPost->setTitle(request()->input('title'));
        $this->em->persist($authorizedPost);
        $this->em->flush();
        return response()->json($authorizedPost);

    }
    public function destroy($id): JsonResponse {
        $authorizedPost = $this->authorizeUserForPost($id);
        if ($authorizedPost instanceof JsonResponse) {
            return $authorizedPost;
        }
        $message = "Категория удалена";
        $authorizedPost = $this->em->getRepository(Category::class)->find($id);
        $this->em->remove($authorizedPost);
        $this->em->flush();
        return response()->json(['message' => $message]);

    }




}
