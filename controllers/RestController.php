<?php

namespace app\controllers;

use app\models\Bearer;
use app\models\Comment;
use app\models\CommentForm;
use app\models\LoginForm;
use app\models\Post;
use app\models\User;
use Yii;
use yii\rest\ActiveController;

class RestController extends ActiveController
{
    public $modelClass = 'app\models\Post';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => Bearer::className(),
            'except' => ['login', 'comments']
        ];
        return $behaviors;
    }

    public function actionLogout()
    {
        $token = explode(" ", Yii::$app->request->headers->get('Authorization'))[1];
        $user = User::findIdentityByAccessToken($token);
        $user->token = "";
        $user->save();
        return ["message" => "Вы успешно вышли"];
    }

    public function actionLogin()
    {
        $token = Yii::$app->request->headers->get('Authorization');
        if (!empty($token)) {
            Yii::$app->response->setStatusCode(403);
            return ["message" => "У вас нет прав"];
        }

        $model = new LoginForm();

        if ($model->load(['LoginForm' => Yii::$app->request->post()])) {
            if (!$model->validate()) {
                Yii::$app->response->setStatusCode(422);
                return $model->getErrors();
            }
            return $model->loginRest($model);
        }
        return ["message" => "Неизвестная ошибка"];
    }

    public function actionComments()
    {
        $comments = Comment::getAll();
        return ["comments" => $comments];
    }

    public function actionCommentCreate($post_id)
    {
        $model = new CommentForm();

        if ($model->load(['CommentForm' => Yii::$app->request->post()])) {
            if (!$model->validate()) {
                Yii::$app->response->setStatusCode(422);
                return $model->getErrors();
            }
            return $model->createComment($post_id, $model);
        }
        return ["message" => "Неизвестная ошибка"];
    }

    public function actionCommentUpdate($id)
    {
        $token = explode(" ", Yii::$app->request->headers->get('Authorization'))[1];
        if (!User::isAdminRest($token)) {
            Yii::$app->response->setStatusCode(403);
            return ["message" => "У вас нет прав"];
        }

        $model = Comment::findIdentity($id);

        if (!$this->request->isPatch) {
            Yii::$app->response->setStatusCode(405);
            return ["message" => "Некорректный метод"];
        }
        if ($model->load(['Comment' => Yii::$app->request->post()]) && $model->save()) {
            Yii::$app->response->setStatusCode(201);
            return ["message" => "Комментарий успешно обновлен", "comment" => $model];
        }
        Yii::$app->response->setStatusCode(400);
        return ["message" => "Неизвестная ошибка"];
    }

    public function actionCommentDelete($id)
    {
        $token = explode(" ", Yii::$app->request->headers->get('Authorization'))[1];
        if (!User::isAdminRest($token)) {
            Yii::$app->response->setStatusCode(403);
            return ["message" => "У вас нет прав"];
        }

        if (!$this->request->isDelete) {
            Yii::$app->response->setStatusCode(405);
            return ["message" => "Некорректный метод"];
        }

        if (!Comment::findIdentity($id)) {
            Yii::$app->response->setStatusCode(400);
            return ["message" => "Такого комментария не существует"];
        } else {
            Comment::deleteComment($id);
            Yii::$app->response->setStatusCode(200);
            return ["message" => "Комменатрий успешно удалён"];
        }


    }


}
