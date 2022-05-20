<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['username', 'password'], 'required', 'message' => "Заполните поле!"],
            ['username', 'unique', 'targetClass' => 'app\models\User', 'message' => "Такой пользователь уже существует!"],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
        ];
    }
    public function signup() {
        $user = new User();
        $user->username = $this->username;
        $user->password = Yii::$app->security->generatePasswordHash($this->password);
        $user->is_admin = 0;
        return $user->save();
    }


}