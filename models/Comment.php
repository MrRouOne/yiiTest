<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property string $description
 * @property string $time
 *
 * @property Post $post
 * @property User $user
 */
class Comment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'comment';
    }

    public function rules()
    {
        return [
            [['post_id', 'user_id', 'description', 'time'], 'required'],
            [['post_id', 'user_id'], 'integer'],
            [['time'], 'safe'],
            [['description'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Запись',
            'user_id' => 'Пользователь',
            'description' => 'Описнаие',
            'time' => 'Время',
        ];
    }

    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function getAll()
    {
        return static::find()->all();
    }

    public static function findByPost($id)
    {
        return static::find()->where(['post_id' => $id])->all();
    }

    public static function createComment($id,$model)
    {
        date_default_timezone_set('Etc/GMT-7');
        $date = date("Y-m-d H:i:s");
        $post_id = Post::findIdentity($id)->id;

        $comment = new Comment();
        $comment->post_id = $post_id;
        $comment->user_id = Yii::$app->user->id;
        $comment->description = $model->description;
        $comment->time = $date;
        return $comment->save();
    }

    public static function deleteComment($id)
    {
        return static::findIdentity($id)->delete();
    }
}
