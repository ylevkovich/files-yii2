<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Admin panel';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'login',
            'pass',
            'email:email',
            'authKey',

            ['class' => ActionColumn::className(),
                'buttons'=>[

                    'delete'=>function ($url, $model) {
                        $customurl=Yii::$app->getUrlManager()->createUrl(['user/delete','id'=>$model['id']]);
                        return Html::a( '<span class="glyphicon glyphicon-trash"></span>', $customurl,
                            ['title' => Yii::t('yii', 'Delete')]);
                    }
                ],
                'template'=>'{view}  {update}  {delete}',
            ],
        ],
    ]); ?>

</div>
