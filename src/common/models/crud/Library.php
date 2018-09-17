<?php

namespace cronfy\library\common\models\crud;

use Yii;

/**
 * This is the model class for table "library".
 *
 * @property integer $id
 * @property string $sid
 * @property integer $pid
 * @property string $name
 * @property string $value
 * @property string $data
 * @property string $image
 * @property string $content
 * @property integer $is_active
 * @property integer $sort
 */
class Library extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'library';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'pid/integer' => ['pid', 'integer'],
            'is_active/integer' => ['is_active', 'integer'],
            'sort/integer' => ['sort', 'integer'],
            'data/string' => ['data', 'string'],
            'content/string' => ['content', 'string'],
            'sid/length' => ['sid', 'string', 'max' => 255],
            'name/length' => ['name', 'string', 'max' => 255],
            'value/length' => ['value', 'string', 'max' => 255],
            'image/length' => ['image', 'string', 'max' => 255],
            'pid,sid/unique' => [['pid', 'sid'], 'unique', 'targetAttribute' => ['pid', 'sid'], 'message' => 'The combination of Sid and Pid has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sid' => 'Sid',
            'pid' => 'Pid',
            'name' => 'Name',
            'value' => 'Value',
            'data' => 'Data',
            'image' => 'Image',
            'content' => 'Content',
            'is_active' => 'Is Active',
            'sort' => 'Sort',
        ];
    }
}
