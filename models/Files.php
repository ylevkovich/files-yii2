<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property integer $id
 * @property integer $id_user
 * @property string $path
 * @property string $share_link
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'path', 'share_link'], 'required'],
            [['id_user'], 'integer'],
            [['path'], 'string'],
            [['share_link'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Id User',
            'path' => 'Path',
            'share_link' => 'Share Link',
        ];
    }

    public function clearDataUser()
    {
        $this->deleteAll([
            'id_user' => Yii::$app->user->identity['id']
        ]);
    }

    public function getFileShareLink($id)
    {
        $ob = $this->findOne($id);
        return yii::$app->id.'/files/get-file?hash='.$ob['share_link'];
    }
}
