<?php
namespace common\repositories;

use common\entities\User;
use yii\web\NotFoundHttpException;

class UserRepository
{
    public function getByEmail(string $email): User
    {
        if (!$user = User::findOne(['email' => $email])) {
            throw new \DomainException('User is not found.');
        }
        return $user;
    }

    public function existByPasswordResetToken(string $token): User
    {
        return (bool)User::findByPasswordResetToken($token);
    }

    public function getByPasswordResetToken(string $token): User
    {
        if (!$user = User::findByPasswordResetToken($token)) {
            throw new \DomainException ('User is not found.');
        }
        return $user;
    }

    public function save(User $user): void
    {
        if (!$user->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }

    private function getBy(array $condition): User
    {
        if (!$user = User::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundHttpException('User not found.');
        }
        return $user;
    }

}