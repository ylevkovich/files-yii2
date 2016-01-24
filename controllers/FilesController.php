<?php

namespace app\controllers;

use Yii;
use app\models\Files;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\UploadForm;
use yii\web\UploadedFile;

/**
 * FilesController implements the CRUD actions for Files model.
 */
class FilesController extends Controller
{
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

    public function uploadFiles()
    {
        $modelUpload = new UploadForm();
        $modelUpload->file = UploadedFile::getInstances($modelUpload, 'file');

        if ($modelUpload->file && $modelUpload->validate()) {
            foreach ($modelUpload->file as $file) {
                $pathToFile = '../upload/' . Yii::$app->user->identity['login'] . '/' . $file->baseName . '.' . $file->extension;

                if ($file->saveAs($pathToFile)) {
                    $modelFiles = new Files();
                    $modelFiles->id_user = Yii::$app->user->identity['id'];
                    $modelFiles->path = $file->baseName . '.' . $file->extension;
                    $modelFiles->share_link = Yii::$app->security->generateRandomString();

                    if (!$modelFiles->save())
                        unlink($pathToFile);

                    $this->goHome();
                }
            }
        }
    }

    public function actionDownload($id)
    {
        $model = new Files();
        $file = $model->getFilePath($id);

        if (file_exists($file)) {
            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            // заставляем браузер показать окно сохранения файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // читаем файл и отправляем его пользователю
            readfile($file);
            exit;
        }
    }

    public function actionDeleteall()
    {
        $model = new Files();
        $pathToDir = '../upload/' . Yii::$app->user->identity['login'];

        $this->removeDirectory($pathToDir);
        $model->clearDataUser();

        $this->goHome();
    }

    public function removeDirectory($dir)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
            }
        }
    }

    /**
     * Deletes an existing Files model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $modelFiles = Files::findOne($id);

        if (unlink('../upload/' . Yii::$app->user->identity['login'] . '/' . $modelFiles->path))
            $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionShare($id)
    {
        $model = new Files();

        return $this->render('share', [
            'url' => $model->getFileShareLink($id),
            ]);
    }

//    public function
}