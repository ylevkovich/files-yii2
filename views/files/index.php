<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use yii\db\ActiveRecord;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Files';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="files-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--    Upload form    -->
    <?php $form = ActiveForm::begin([
        'options' =>[
            'enctype' => 'multipart/form-data'
        ]
    ]) ?>
    <?= $form->field($model, 'file[]')->fileInput(['multiple' => true]) ?>

    <button class="btn btn-success">Upload chose files</button>
    <?= Html::a('Delete all files', ['deleteall'], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete all your files?',
            'method' => 'post',
        ],
    ]) ?>

    <?php ActiveForm::end();
    $obActionColumn = new ActionColumn();
    $obActiveRecord = new ActiveRecord();
    ?>
    <!--    Upload form end   -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'path:ntext',
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
                        $customurl=Yii::$app->getUrlManager()->createUrl(['files/download','id'=>$model['id']]);
                        return Html::a( '<span class="glyphicon glyphicon-download"></span>', $customurl,
                            ['title' => Yii::t('yii', 'Download')]);
                    }
                ],
                'template'=>'{share}  {download}  {delete}',
            ],
        ],
    ]); ?>

</div>
