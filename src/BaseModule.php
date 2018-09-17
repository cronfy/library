<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 23.10.17
 * Time: 18:34
 */

namespace cronfy\library;

use cronfy\library\common\misc\BusinessLogic;
use cronfy\library\common\misc\LibraryRepository;
use cronfy\library\common\models\CustomProperties;
use Yii;
use yii\base\Module;

class BaseModule extends Module
{
    public $overrideDir;
    public $customPropertiesClass = CustomProperties::class;
    public $businessLogicClass = BusinessLogic::class;

    public $libraryRepositoryDefinition = [
        'class' => LibraryRepository::class,
    ];

    protected $_libraryRepository;
    /**
     * @return LibraryRepository
     * @throws \yii\base\InvalidConfigException
     */
    public function getLibraryRepository() {
        if (!$this->_libraryRepository) {
            $this->_libraryRepository =  Yii::createObject($this->libraryRepositoryDefinition);
        }

        return $this->_libraryRepository;
    }

    public function getCustomPropertiesClass()
    {
        return $this->customPropertiesClass;
    }

    protected $_businessLogic;
    /**
     * @return BusinessLogic
     */
    public function getBusinessLogic()
    {
        if (!$this->_businessLogic) {
            $this->_businessLogic = new $this->businessLogicClass;
        }

        return $this->_businessLogic;
    }

    public function getControllerPath()
    {
        // Yii определяет путь к контроллеру через алиас по controllerNamespace.
        // Алиас создавать не хочется, так как это лишняя сущность, которая может
        // конфликтовать с другими алиасами (мы - модуль и не знаем, какие алиасы
        // уже используются в приложении). Поэтому определим путь к контроллерам
        // своим способом.
        $rc = new \ReflectionClass(get_class($this));
        return dirname($rc->getFileName()) . '/controllers';
    }

}
