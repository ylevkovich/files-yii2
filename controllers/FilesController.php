<?php

namespace app\controllers;

use Yii;
use app\models\Files;
use yii\base\Exception;
use app\components\GoodException;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\UploadForm;
use yii\web\UploadedFile;

/**
 * FilesController implements the CRUD actions for Files model.
 */
class FilesController extends Controller
{
    /**
     * @return array behavior rules
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'controllers' => ['files'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'controllers' => ['files'],
                        'actions' => ['get_file_by_code'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all Files models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Files::find()
                ->where(['id_user' => Yii::$app->user->identity['id']])
                ->orderBy('id DESC'),
        ]);

        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $this->uploadFiles();
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Uploads files to user's directory
     * @return string exception message if error
     * @throws GoodException if data not saved to DB
     */
    protected function uploadFiles()
    {
        try{
            $modelUpload = new UploadForm();
            $modelUpload->file = UploadedFile::getInstances($modelUpload, 'file');

            if( $modelUpload->file && $modelUpload->validate() ){
                foreach ($modelUpload->file as $file){
                    $pathToFile = '../upload/' . Yii::$app->user->identity['login'] . '/' . $file->baseName . '.' . $file->extension;

                    if( !$file->saveAs($pathToFile) )
                        throw new GoodException('Error', 'Can\'t save file...');

                    $modelFiles = new Files();
                    $modelFiles->id_user = Yii::$app->user->identity['id'];
                    $modelFiles->path = $file->baseName . '.' . $file->extension;
                    $modelFiles->share_link = Yii::$app->security->generateRandomString();

                    if( !$modelFiles->save() ){
                        unlink($pathToFile);
                        throw new GoodException('Error','Save data error...');
                    }

                    $this->goHome();
                }
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Allows download his file by id
     * @param $id_file integer
     * @throws GoodException if field with this id doesn't exist
     */
    public function actionDownload_his_file($id_file)
    {
        $this->downloadFileByPath(self::getFilePathById($id_file));
    }

    /**
     * Downloads a file from a specified path
     * @param $path string. Path to file
     * @return string
     * @throws GoodException if file doesn't exist
     */
    protected function downloadFileByPath($path)
    {
        try{
            if( !file_exists($path) )
                throw new GoodException('Error', 'No such file...');

            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($path));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));

            readfile($path);
            exit;

        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Deletes files in user's directory and
     * clears all in DB
     */
    public function actionDeleteall()
    {
        $model = new Files();
        $pathToDir = '../upload/' . Yii::$app->user->identity['login'];

        $this->cleanDirectory($pathToDir);
        $model->clearDataUser();

        $this->goHome();
    }

    /**
     * Cleans directory
     * @param $dir string. Path to directory
     */
    protected function cleanDirectory($dir)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->cleanDirectory($obj) : unlink($obj);
            }
        }
    }

    /**
     * Deletes an existing Files model.
     * If deletion is successful, the browser will be redirected to the home page.
     * @param integer $id
     * @return $this redirect to home url
     * @throws GoodException user try to delete doesn't own file and if file doesn't exist
     */
    public function actionDelete($id)
    {
        try{
            if( !$modelFiles = Files::findOne($id))
                throw new GoodException('Error', 'Doesn\'t exist field with this id...');

            $filePath = self::getFilePathById($id);
            if( file_exists ($filePath) )
                unlink($filePath);

            if( !$modelFiles->delete() )
                throw new GoodException('Error', 'Deleting file error...');
        }catch(Exception $e){
            return $e->getMessage();
        }

        return $this->goHome();
    }

    /**
     * Takes hashcode for sharing file.
     * @param integer $id id file to sharing.
     * @return mixed
     * @throws
     */
    public function actionShare($id)
    {
        try{
            if( !$file = Files::findOne($id) )
                throw new GoodException('Error', 'Doesn\'t exist field with this id...');

            if( Yii::$app->user->identity['id'] != $file['id_user'] )
                throw new GoodException('Error', 'Wrong file id...');

            return $this->render('share', [
                'url' => self::getFileShareLinkByObj($file),
            ]);

        }catch(Exception $e){
            $e->getMessage();
        }

        return false;
    }

    /**
     * Returns link to downloading the file
     * @return string link to the file
     */
    protected static function getFileShareLinkByObj($file)
    {
        return yii::$app->request->getHostInfo().Yii::$app->homeUrl.'files/get_file_by_code?code='.$file['share_link'];
    }

    /**
     * Takes file link by id.
     * @param integer $id id file.
     * @return string link to file or false if error
     * @throws GoodException if field with this id doesn't exist
     */
    protected static function getFilePathById($id)
    {
        try{
            if( !$file = Files::findOne($id) )
                throw new GoodException('Error', 'Doesn\'t exist field with this id...');

            if( Yii::$app->user->identity['id'] != $file['id_user'] )
                throw new GoodException('Error', 'Wrong file id...');

            return '../upload/'.Yii::$app->user->identity['login'].'/'.$file['path'];
        }catch(Exception $e){
            $e->getMessage();
        }

        return false;
    }

    /**
     * Downloads file by hash-code
     * @param $code string
     * @return bool|string
     * @throws GoodException if no one file doesn't linked by code
     */
    public function actionGet_file_by_code($code)
    {
        try{
            if( !$file = Files::find()->with('user')->where(['share_link' => $code])->one() )
                throw new GoodException('Error', 'No such file linked to this code...');

            return $this->downloadFileByPath('../upload/'.$file->user->login.'/'.$file->path);
        }catch(Exception $e){
            $e->getMessage();
        }

        return false;
    }
}