<?php

namespace app\models;

use Yii;
use yii\base\Model;

class loginForm extends Model
{
    public $login;
    public $pass;

    public function rules()
    {
        return [
            [['login', 'pass'], 'required'],
            [['login', 'pass'], 'string', 'max' => 255],
            ['pass', 'validatePassword']
        ];
    }

    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()):
            $user = User::findByLogin($this->login);
            if (!$user || !$user->validatePassword($this->pass)):
                $this->addError($attribute, 'Not correct login or password.');
            endif;
        endif;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'pass' => 'Password',
        ];
    }

    public function login()
    {
        $user = User::findByLogin($this->login);

        if ($this->validate() && $user):
            return Yii::$app->user->login($user, 3600*24*30);
        else:
            return false;
        endif;
    }


}
