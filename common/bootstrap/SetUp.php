<?php
/**
 * Created by PhpStorm.
 * User: andri
 * Date: 10.10.17
 * Time: 16:03
 */

namespace common\bootstrap;

use yii\base\BootstrapInterface;
use frontend\services\auth\PasswordResetService;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        $container->set('user.password_reset', function () use ($app) {
            return new PasswordResetService([$app->params['supportEmail'] => $app->name . ' robot'],);
        });
    }
}