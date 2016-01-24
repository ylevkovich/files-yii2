<?php

namespace app\models;

use yii\base\Model;
use Yii;

class RegForm extends Model
{
    public $login;
    public $pass;
    public $email;
    public $authKey;

    public function rules()
    {
        return [
            [['login', 'pass', 'email'], 'required', 'message' => 'Please choose this field'],
            [['login', 'pass', 'email', 'authKey'], 'string', 'max' => 255],
            ['login', 'unique',
                'targetClass' => user::className(),
                'message' => 'this login already exist.'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => user::className(),
                'message' => 'this email already exist.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'pass' => 'Password',
            'email' => 'Email',
        ];
    }

    public function reg()
    {
        $user = new User();

        $user->login = $this->login;
        $user->setPassword($this->pass);
        $user->email = $this->email;
        $user->generateAuthKey();
        return $user->save() ? $user : null;
    }
}
