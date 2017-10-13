<?php

namespace common\services;

use common\entities\User;
use common\repositories\UserRepository;
use common\forms\LoginForm;

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