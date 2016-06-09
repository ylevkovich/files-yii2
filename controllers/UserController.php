<?php

namespace app\controllers;

use app\components\GoodException;
use app\models\User;
use Yii;
use app\Models\RegForm;
use app\Models\LoginForm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\base\Exception;

class UserController extends Controller
{
    /**
     * @return array behavior rules
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                        'actions' => ['reg', 'login'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => ['index', 'logout'],
                    ],
                ]
            ]
        ];
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest):
            return $this->goHome();
        endif;

        $model = new LoginForm();

        if( $model->load(Yii::$app->request->post()) && $model->login() ):
            return $this->goHome();
        endif;

        return $this->render('login',['model' => $model]);
    }

    public function actionReg()
    {
        $model = new RegForm();

        if( $model->load(Yii::$app->request->post()) && $model->validate() ):
            if($user = $model->reg()):
                if( mkdir("../upload/$model->login", 0700) ):
                    if( Yii::$app->getUser()->login($user) ):
                        return $this->goHome();
                    endif;
                else:
                    Yii::$app->session->setFlash('error', 'Creating folder error.');
                    Yii::error('Creating folder error.');
                    return $this->refresh();
                endif;
            else:
                Yii::$app->session->setFlash('error', 'Registration error.');
                Yii::error('Registration error.');
                return $this->refresh();
            endif;
        endif;

        return $this->render('reg',['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['files/index']);
    }

    /**
     * Shows all registered users for administrating
     * @return string message
     * @throws GoodException if user is not admin
     */
    public function actionIndex()
    {
        try{
            if( Yii::$app->user->identity->login != 'admin')
                throw new GoodException('Error', 'Deny access...');

            $dataProvider = new ActiveDataProvider([
                'query' => User::find(),
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new User();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        //TODO deleting user folder
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
