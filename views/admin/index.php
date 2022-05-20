<?php

/** @var yii\web\View $this */

use app\models\Comment;
use app\models\User;
use yii\bootstrap4\Html;

$this->title = 'Админка';

?>
    <style>
        img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 50%;
            border: 1px solid black;
        }
    </style>

    <h1 class="text-center">Админка</h1>

<?php
echo Html::a('Создать запись', ['post-create'], ['class' => 'btn btn-primary ml-5']);

$baseURL = Yii::$app->request->baseUrl;
foreach ($posts as $post) {
    $comments = Comment::findByPost($post->id);
    echo "<div class='card mt-5 ml-5'><div class='card-body'>" .
        "<h3 class='card-title'>$post->title</h3>" .
        "<h5 class='card-text'>$post->description</h5>";
    echo Html::a('Редактировать', ['post-update', 'id' => $post->id], ['class' => 'btn btn-primary mr-3']) .
        Html::a('Удалить', ['post-delete', 'id' => $post->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены?',
                'method' => 'post',
            ]]);

    echo "</div><div class='card-footer text-muted'>" . date_format(date_create($post->time), "j F Y H:i") . "</div></div>";
    foreach ($comments as $comment) {
        $owner = User::findIdentity($comment->user_id);
        echo "<div class='card ml-5'><div class='card-body'>" .
            "<h5 class='card-title'><img src='$baseURL/web/img/non.png' class='mr-2'>$owner->username</h5>" .
            "<p class='card-text'>$comment->description</p>";
        echo Html::a('Редактировать', ['comment-update', 'id' => $comment->id], ['class' => 'btn btn-primary mr-3']) .
            Html::a('Удалить', ['comment-delete', 'id' => $comment->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены?',
                    'method' => 'post',
                ]]);
        echo "</div><div class='card-footer text-muted'>" . date_format(date_create($comment->time), "j F Y H:i") . "</div></div>";
    }
}

?>