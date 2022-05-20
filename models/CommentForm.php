<?php

namespace app\models;

use Yii;
use yii\base\Model;

class CommentForm extends Model
{
    public $description;

    public function rules()
    {
        return [
            [['description'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'description' => 'Оставить комментарий',
        ];
    }

    public function createComment($post_id, $model)
    {
        $token = explode(" ", Yii::$app->request->headers->get('Authorization'))[1];
        date_default_timezone_set('Etc/GMT-7');
        $date = date("Y-m-d H:i:s");

        $comment = new Comment();
        $comment->post_id = $post_id;
        $comment->user_id = User::findIdentityByAccessToken($token)->id;
        $comment->description = $model->description;
        $comment->time = $date;
        $comment->save();
        return ["message" => "Комментарий успешно создан", "comment" => $comment];

    }


}