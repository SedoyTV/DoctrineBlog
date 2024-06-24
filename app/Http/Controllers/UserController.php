<?php

namespace App\Http\Controllers;
use App\Services\UserServices;

class UserController
{
    public UserServices $userServices;
    public function __construct(UserServices $userServices) {
        $this->userServices = $userServices;
    }

    public function register()
    {
        return $this->userServices->register();
    }
    public function login(){
        return $this->userServices->login();
    }
}
