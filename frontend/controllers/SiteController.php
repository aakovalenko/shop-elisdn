<?php
namespace frontend\controllers;


use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use shop\forms\auth\PasswordResetRequestForm;
use shop\forms\auth\ResetPasswordForm;
use shop\forms\auth\SignupForm;
use shop\forms\ContactForm;
use yii\web\NotFoundHttpException;
use shop\services\ContactService;
use shop\services\auth\PasswordResetService;
use shop\services\auth\AuthService;
use shop\services\auth\SignupService;


/**
 * Site controller
 */
class SiteController extends Controller
{
    private $authService;
    private $signupService;
    private $passwordResetService;
    private $contactService;

    public function __construct(
        $id,
        $module,
        AuthService $authService,
        SignupService $signupService,
        PasswordResetService $passwordResetService,
        ContactService $contactService,
        $config = [])
    {
        parent::__construct($id,$module,$config);
        $this->authService = $authService;
        $this->signupService = $signupService;
        $this->passwordResetService = $passwordResetService;
        $this->contactService = $contactService;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }





    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            try{
                $this->contactService->send($form);
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
                return $this->goHome();
            }  catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }
            return $this->render('contact', [
                'model' => $model,
            ]);

    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $form = new SignupForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {

            try{
                $this->signupService->signup($form);
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();

            }catch (\DomainException $e){
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('signup', [
            'model' => $form,
        ]);
    }



 /*   public function actionCheckout()
    {
        $form = new CheckoutForm();
         if ($form->load(Yii::$app->request->post()) && $form->validate()) {


             (new OrderService())->checkout(Yii::$app->user->id, $form);

            return $this->refresh();
         }
         return $this->render('signup', [
            'model' => $form,
         ]);
    }*/



    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $form = new PasswordResetRequestForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->passwordResetService->request($form);
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } catch (\DomainException $e){
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $form,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {

        try {
            $this->passwordResetService->validateToken($token);
        } catch (\DomainException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $form = new ResetPasswordForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {

            try{
                $this->passwordResetService->reset($token, $form);
                Yii::$app->session->setFlash('success', 'New password saved.');
                return $this->goHome();

            } catch (\DomainException $e){
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

        }

        return $this->render('resetPassword', [
            'model' => $form,
        ]);
    }
}

class OrderService
{
    public function checkout($userId, $form): void
    {
        $user = $this->findUser($userId);

        if ($user->isBanned()) {
            throw new \DomainException();
        }

        $cart = $this->findCart($user->id);

        if (!$cart->hasItems()) {
            throw new \DomainException();
        }

        $order = new Order($form->name);

        foreach ($cart->getItems() as $item) {
            $order->addItem($item->getProduct(), $item->getAmount());
        }

        Yii::$app->db->trasaction(function () use ($order, $user, $cart) {
            $order->save();
            $user->save();
            $cart->clear();
            $this->saveCart($cart);
        });
    }

    private function findUser($userId): User
    {
        if (!$user = User::findOne($userId)) {
            throw new NotFoundHttpException();
        }
        return $user;
    }
}