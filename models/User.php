<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {

    }

    public function validateAuthKey($authKey)
    {

    }

    public function validatePassword($password)
    {
      return \Yii::$app->security->validatePassword($password, $this->password);
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['user_id' => 'id']);
    }

    public static function isAdmin($id)
    {
        if (Yii::$app->user->isGuest or !static::findIdentity($id)->is_admin) {
            return false;
        }
        return true;
    }

    public static function isAdminRest($token)
    {
        if (!static::findIdentityByAccessToken($token)->is_admin) {
            return false;
        }
        return true;
    }

}
