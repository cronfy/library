<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 16.12.15
 * Time: 11:37
 */

namespace cronfy\library\common\models;

use yii\db\ActiveQuery;

class ConcreteLibraryQuery extends ActiveQuery
{

    public function init()
    {
        /** @var ConcreteLibrary $active_record_class */
        $active_record_class = $this->modelClass;
        $root = $active_record_class::getRoot();
        parent::init();
        $this->alias($active_record_class::alias())
            ->andWhere(['pid' => $root->id]);
    }

    public function where($condition, $params = [])
    {
        // чтобы не затерлись where, которые в init()
        return parent::andWhere($condition, $params);
    }
}
