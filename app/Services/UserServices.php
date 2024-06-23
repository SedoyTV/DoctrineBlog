<?php

namespace App\Services;

use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserServices
{
    public EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $data = request()->json()->all();
        $current_user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if ($current_user) {
            return response()->json(['error' => 'Пользователь с таким email уже существует']);
        }

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword(Hash::make($data['password']));

        $this->em->persist($user);
        $this->em->flush();

        $token = JWTAuth::fromUser($user);
        $userData = $this->getUserData($token);

        return response()->json(['token' => $token, 'user' => $userData]);
    }

    public function login()
    {
        $data = request()->json()->all();
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user || !Hash::check($data['password'], $user->getPassword())) {
            return response()->json(['error' => 'Неверные данные'], 401);
        }

        $token = JWTAuth::fromUser($user);
        $userData = $this->getUserData($token);

        return response()->json(['token' => $token, 'user' => $userData]);
    }

    private function getUserData($token)
    {
        $user = JWTAuth::setToken($token)->toUser();

        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];
    }
}
