<?php

namespace app\controllers;

use app\models\Comment;
use app\models\CommentForm;
use app\models\Post;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;

class AdminController extends Controller
{

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

    public function actionIndex()
    {
        if (!User::isAdmin(Yii::$app->user->id)) {
            return $this->goHome();
        }

        return $this->render('index', [
                'posts' => Post::getAll(),
            ]
        );
    }

    public function actionPostCreate()
    {
        if (!User::isAdmin(Yii::$app->user->id)) {
            return $this->goHome();
        }

        $model = new Post();
        if ($model->load(Yii::$app->request->post()) and $model->validate()) {
            if ($model->createPost()) {
                $this->redirect(['index']);
            }
        }

        return $this->render('postCreate', [
            'model' => $model,
        ]);
    }

    public function actionPostDelete($id)
    {
        if (!User::isAdmin(Yii::$app->user->id)) {
            return $this->goHome();
        }

        Post::deletePost($id);

        return $this->redirect(['index']);
    }

    public function actionPostUpdate($id)
    {
        if (!User::isAdmin(Yii::$app->user->id)) {
            return $this->goHome();
        }

        $model = Post::findIdentity($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('postUpdate', [
            'model' => $model,
        ]);
    }

    public function actionCommentDelete($id)
    {
        if (!User::isAdmin(Yii::$app->user->id)) {
            return $this->goHome();
        }

        Comment::deleteComment($id);

        return $this->redirect(['index']);
    }

    public function actionCommentUpdate($id)
    {
        if (!User::isAdmin(Yii::$app->user->id)) {
            return $this->goHome();
        }

        $model = Comment::findIdentity($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('commentUpdate', [
            'model' => $model,
        ]);
    }

}
