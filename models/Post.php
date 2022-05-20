<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $time
 *
 * @property Comment[] $comments
 */
class Post extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'post';
    }

    public function rules()
    {
        return [
            [['title', 'description'], 'required'],
            [['time'], 'safe'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'time' => '',
        ];
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function getAll()
    {
        return static::find()->all();
    }

    public function createPost()
    {
        date_default_timezone_set('Etc/GMT-7');
        $date = date("Y-m-d H:i:s");

        $post = $this;
        $post->title = $this->title;
        $post->description = $this->description;
        $post->time = $date;
        return $post->save();
    }

    public static function deletePost($id)
    {
        return static::findIdentity($id)->delete();
    }
}
