<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 18.01.2016
 * Time: 1:07
 */

namespace app\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\filters\AccessControl;

class BehaviorsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ()
                {
                    throw new Exception('Deny access...');
                },
                'rules' => [
                    [
                        'allow' => true,
                        'controllers' => ['user'],
                        'actions' => ['login', 'reg'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['user'],
                        'actions' => ['logout'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['site'],
                        'actions' => ['contact'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['site'],
                        'verbs' => ['GET', 'POST']
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['file'],
                        'actions' => ['get_file_by_code'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'matchCallback' => function()
                        {
                            if(Yii::$app->user->identity['login'] === 'admin')
                                return true;
                        }
                    ],
                ]
            ]
        ];
    }
}