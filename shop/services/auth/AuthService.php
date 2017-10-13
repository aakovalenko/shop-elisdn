<?php

namespace shop\auth\services;

use shop\entities\User;
use shop\repositories\UserRepository;
use shop\forms\auth\LoginForm;

class AuthService
{
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function auth(LoginForm $form): User
    {
        $user = $this->users->findByUsernameOrEmail($form->username);
        if (!$user || !$user-> isActive() || !$user->validatePassword($form->password)) {
            throw new \DomainException('underfined user or password.');
        }
        return $user;
    }
}