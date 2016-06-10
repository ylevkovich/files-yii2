<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->registerJsFile('web/js/files/index.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = 'Work panel';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="files-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--    Upload form    -->
    <div class="panel panel-info">
        <div class="panel-heading">You can add files</div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'options' =>[
                    'enctype' => 'multipart/form-data'
                ]
            ]) ?>
            <?= $form->field($model, 'file[]')->fileInput(['multiple' => true,'class' => 'btn btn-default']) ?>
            <?= Html::a('Delete all files', ['deleteall'], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete all your files?',
                    'method' => 'post',
                ],
            ]) ?>
            <?php ActiveForm::end();?>
        </div>
    </div>
    <!--    Upload form end   -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'path:ntext',
            'size:ntext',
            ['class' => ActionColumn::className(),
                'buttons'=>[
                    'share'=>function ($url, $model) {
                        $customurl=Yii::$app->getUrlManager()->createUrl([
                            'files/share',
                            'id'=>$model['id']
                        ]);
                        return Html::a(
                            '<span class="glyphicon glyphicon-share"></span>',
                            $customurl,
                            [
                                'title' => Yii::t('yii', 'Share')
                            ]);

                    },
                    'download'=>function ($url, $model) {
                        $customurl=Yii::$app->getUrlManager()->createUrl(['files/download_his_file','id_file'=>$model['id']]);
                        return Html::a( '<span class="glyphicon glyphicon-download"></span>', $customurl,
                            ['title' => Yii::t('yii', 'Download')]);
                    }
                ],
                'template'=>'{share}  {download}  {delete}',
            ],
        ],
    ]); ?>

</div>
