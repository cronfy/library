<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 16.12.15
 * Time: 11:37
 */

namespace cronfy\library\common\models;

use cronfy\library\common\misc\LibraryHelper;

/**
 * Нужно определить либо myNamespace(), либо mandatoryAttributes().
 */
abstract class ConcreteLibrary extends Library
{

    public static function find()
    {
        return new ConcreteLibraryQuery(get_called_class());
    }

    public static function updateAll($attributes, $condition = '', $params = [])
    {
        // Так как данные хранятся в иерархии, обновить все элемента справочника
        // одним запросом не получится - нужно рекурсивно проходить по всем нодам,
        // у которых есть потомки.
        // Пока отключаем эту возможность, до момента, когда она реально потребуется.
        // Впрочем, если в condition передаются id, то ок.
        if (!(is_array($condition) && array_key_exists('id', $condition))) {
            throw new \Exception("Disabled: not implemented");
        }

        return parent::updateAll($attributes, $condition, $params);
    }

    public static function deleteAll($condition = '', $params = [])
    {
        // Так как данные хранятся в иерархии, удалить все элемента справочника
        // одним запросом не получится - нужно рекурсивно проходить по всем нодам,
        // у которых есть потомки.
        // Пока отключаем эту возможность, до момента, когда она реально потребуется.
        throw new \Exception("Disabled: not implemented");
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     *
     * Элемент должен попасть в нужное место в иерархии, но будет неудобно, если придется каждый
     * раз в коде при создании объекта вспоминать, что нужно назначать ему pid. Сделаем это автоматически.
     * По умолчанию элемент становится потомком корня справочника.
     *
     * Это нужно только для save(). Для update() предаполагаем, что все уже было сохранено, когда запись создавалась
     * в БД.
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->isNewRecord) {
            if (!$this->pid) {
                $this->pid = static::getRoot()['id'];
            }
        }
        return parent::save($runValidation, $attributeNames);
    }

    
    public static function alias()
    {
        $root = static::getRoot();
        return 'library_' . $root->id;
    }

    abstract public static function getRootPath();

    protected static $_cachedRoots;
    public static function getRoot()
    {
        $path = static::getRootPath();
        $key = json_encode($path);
        if (!@static::$_cachedRoots[$key]) {
            static::$_cachedRoots[$key] = LibraryHelper::getByPath($path);
        }
        $root = static::$_cachedRoots[$key];
        return $root;
    }
}
