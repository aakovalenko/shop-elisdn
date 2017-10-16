<?php
namespace frontend\controllers;


use Yii;

use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use shop\forms\auth\PasswordResetRequestForm;
use shop\forms\auth\ResetPasswordForm;

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


}