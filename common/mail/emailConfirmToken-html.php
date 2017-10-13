<?php
/**
 * Created by PhpStorm.
 * User: andri
 * Date: 12.10.17
 * Time: 17:11
 */
use yii\helpers\Html;


$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['site/confirm', 'token' => $user->email_confirm_token]);
?>

<div class="password-reset">
    <p>Hello <?= html::encode($user->username) ?>,</p>
    <p>Follow the link below to confirm your email:</p>
    <p><?= Html::a(Html::encode($confirmLink), $confirmLink) ?></p>
</div>
