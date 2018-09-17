<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 07.09.17
 * Time: 11:49
 */

namespace cronfy\library\common\models;

use cronfy\experience\php\tree\TreeTrait;
use cronfy\library\common\models\Library;
use yii\base\BaseObject;

class LibraryRoot extends BaseObject
{
    use TreeTrait;

    public $id = null;
    public $is_active = true;

    public function getName()
    {
        return 'Справочники';
    }

    public function getIsRootNode()
    {
        return true;
    }

    public function getChildNodesInitializer()
    {
        return function ($knownChildNodes) {
            $result = [];
            foreach (Library::find()->where(['pid' => null])->all() as $child) {
                $found = null;
                if ($knownChildNodes) {
                    foreach ($knownChildNodes as $k => $knownChildNode) {
                        if ($knownChildNode->id == $child->id) {
                            $found = $knownChildNode;
                            unset($knownChildNodes[$k]);
                            break;
                        }
                    }
                }
                $result[] = $found ?: $child;
            }

            return $result;
        };
    }
}
