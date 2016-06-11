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
                        'actions' => ['index', 'logout', 'view', 'update', 'delete'], //view,update,delete only for admin
                    ],
                ]
            ]
        ];
    }

    /**
     * Logins users
     * @return string|\yii\web\Response
     */
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

    /**
     * Registers new user
     * @return string|\yii\web\Response
     * @throws GoodException if registration error or creating folder error
     */
    public function actionReg()
    {
        try{
            $model = new RegForm();
            if( $model->load(Yii::$app->request->post()) && $model->validate() ){
                if( !$user = $model->reg())
                    throw new GoodException('Error', 'Registration error...');

                $pathToDir = "../upload/$model->login";
                if( !is_dir($pathToDir) ){
                    if( !mkdir($pathToDir, 0700) )
                        throw new GoodException('Error','Creating folder error...');
                }else{
                    FilesController::cleanDirectory($pathToDir);
                    if( !empty(scandir($pathToDir)[2]) )
                        throw new GoodException('Error','Files in that directory can\'t be deleted...');
                }

                Yii::$app->getUser()->login($user);
                return $this->goHome();
            }

            return $this->render('reg',['model' => $model]);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Logs out user
     * @return \yii\web\Response
     */
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

    /**
     * Shows all info about user
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Allows edit data by admin
     * @param $id
     * @return string|\yii\web\Response
     * @throws GoodException if user isn't admin
     */
    public function actionUpdate($id)
    {
        try{
            if( Yii::$app->user->identity->login != 'admin')
                throw new GoodException('Error', 'Deny access...');

            $model = $this->findModel($id);

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Deletes user by id. Only for admin
     * @param $id integer user's id to delete
     * @return string|\yii\web\Response
     * @throws GoodException if user not admin, if  data can't be deleted
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        try{
            if( Yii::$app->user->identity->login != 'admin')
                throw new GoodException('Error', 'Deny access...');

            $user = $this->findModel($id);
            if( !$user->delete() )
                throw new GoodException('Error','Can\'t delete data...');

            $this->deleteUsersDir($user->login);

            return $this->redirect(['index']);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Deletes user files and directory by id. Only for admin
     * @param $login. User login to deleting
     * @return string
     * @throws GoodException if user not admin,
     * if files in directory can't be deleted
     * if user's directory can't be deleted
     */
    protected function deleteUsersDir($login){
        try{
            if( Yii::$app->user->identity->login != 'admin')
                throw new GoodException('Error', 'Deny access...');
            
            $pathToDir = '../upload/' . $login;
            FilesController::cleanDirectory($pathToDir);
            if( !empty(scandir($pathToDir)[2]) )
                throw new GoodException('Error','User\'s files can\'t delete...');

            if( !rmdir($pathToDir) )
                throw new GoodException('Error','Can\'t delete user\'s directory');

        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @param $id. User's id
     * @return null|static
     * @throws NotFoundHttpException if requested page does not exist
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
