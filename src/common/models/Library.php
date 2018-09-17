<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 16.12.15
 * Time: 11:37
 */

namespace cronfy\library\common\models;

use cronfy\experience\php\tree\TreeTrait;
use App;
use cronfy\experience\yii2\ensureSave\EnsureSaveTrait;
use cronfy\experience\yii2\JsonModelTrait;
use cronfy\library\BaseModule;
use mohorev\file\UploadBehavior;
use paulzi\jsonBehavior\JsonBehavior;
use paulzi\jsonBehavior\JsonField;
use Yii;
use yii\helpers\FileHelper;

/**
 * Справочник - иерархическая структура элементов.
 *
 * У каждой записи есть napespace, pid и sid, определяющие, к чему она относится.
 *      pid - id родителя, если null, то это корневой элемент (т. е. сам справочник, все элементы внутри него)
 *      sid - уникальный id внутри родителя
 *
 * @property JsonField $data
 */

class Library extends \cronfy\library\common\models\crud\Library
{

    use TreeTrait;
    use EnsureSaveTrait;
    use JsonModelTrait;

    /*
     * ACTIVE RECORD
     */

    public static function tableName()
    {
        return 'library';
    }

    /*
     * TREE TRAIT
     */

    public function getIsRootNode()
    {
        return !$this->pid;
    }

    /**
     * @deprecated вся реализация в getParentNodeInitializer(), там же должен использоваться
     * способ получения parent, который кеширует запросы по одному и тому же id.
     * Этот метод слишком дорогой: когда в цикле для каждого элемента справочника получаешь
     * его parent (например, для построения дерева), на каждый getParent() идет запрос в БД.
     * protected - потому что напрямую нельзя, можно только через getParentNode(), это же TreeTrait.
     * @return \yii\db\ActiveQuery
     */
    protected function getParent()
    {
        return $this->hasOne(Library::class, ['id' => 'pid']);
    }

    /**
     * не protected - потому что часто нужно дернуть child по какому-то where из БД.
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Library::class, ['pid' => 'id']);
    }

    protected function getChildNodesInitializer()
    {
        return function ($knownChildNodes) {
            $result = [];
            foreach ($this->children as $child) {
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

    protected function getParentNodeInitializer()
    {
        return function () {
            if (!$this->pid) {
                return new LibraryRoot();
            }

            $module = $this->getModule();
            $libraryRepository = $module->getLibraryRepository();

            $parent = $libraryRepository->getById($this->pid);

            return $parent;
        };
    }
    
    /*
     * BEHAVIORS
     */

    public function behaviors()
    {
        $behaviors = [
            [
                'class' => JsonBehavior::class,
                'attributes' => ['data'],
            ],
        ];

        if (class_exists(UploadBehavior::class)) {
            $behaviors['uploadCover'] = [
                'class' => UploadBehavior::class,
                'attribute' => 'image',
                'scenarios' => ['insert', 'update'],
                'path' => '@webroot/uploads/library/{imagesPath}/{id}',
                'url' => '/uploads/library/{imagesPath}/{id}',
            ];
        }

        return $behaviors;
    }

    /**
     * От этого нужно избавиться (в приложении может быть несколько модулей
     * library или просто модуль может называться по-другому).
     * Но пока непонятно, как это сделать.
     * Нам нужно, чтобы модель знала, в каком модуле она работает. Но мы получаем
     * модель из БД через Library::findOne(), то есть через статический метод,
     * в который не передашь тот или иной модуль в зависимости от ситуации.
     * Варианты решения:
     * 1. Делать find() через модуль, например Yii::$app->getModule('module_name')->find().
     * Там использовать свой ActiveQuery, в который будет передаваться модуль, который делает зарпос.
     * Так как модель у нас всего одна, это должно сработать.
     * 2. Делать на каждый модуль свою модель, расширяющую Library, в которой зашито получение
     * правильного модуля через getModule(), а эту модель сделать абстрактной.
     *
     * Оба варианта неудобны, поэтому нужно подумать, какой будет более правильным. Возможно все-таки
     * первый, так как в первом варианте не хардкодится имя модуля.
     *
     * @return BaseModule
     */
    public function getModule()
    {
        return Yii::$app->getModule('library');
    }

    public function jsonModelsDefinition()
    {
        return [
            'properties' => [
                'jsonBehaviorAttribute' => 'data',
                'populator' => function ($data) {
                    $propertiesDefinition = $data ?: [];
                    $propertiesClass = $this->getModule()->getCustomPropertiesClass();
                    /** @var CustomProperties $properties */
                    $properties = new $propertiesClass();
                    $properties->setDefinition($propertiesDefinition);
                    $properties->owner = $this;
                    return $properties;
                }
            ]
        ];
    }

    /**
     * @return CustomProperties
     */
    public function getProperties()
    {
        /** @var CustomProperties $properties */
        $properties = $this->getJsonModel('properties');
        return $properties;
    }

    protected static $_cachedElements;
    public function getImagesPath()
    {
        $currentObject = $this;
        while (true) {
            static::$_cachedElements[$currentObject['id']] = $currentObject;

            if ($currentObject->pid === null) {
                break;
            }

            if (isset(static::$_cachedElements[$currentObject->pid])) {
                $currentObject = static::$_cachedElements[$currentObject->pid];
            } else {
                // берем объект целиком. Можно было бы попробовать сэкономить, сделав
                // запрос в БД только чтобы получить scalar() pid (чтобы не инициализировать
                // модель целиком), но довольно часто полученный элемент может оказаться корневым,
                // и все равно придется вытаскивать его целиком.
                
                // Обращаемся не через getParentNode(), а напрямую в БД,
                // потому что объект может быть в дереве с переопределенным корнем, а нам нужно явно получить корневой элемент
                // иерархии по БД.
                $currentObject = $currentObject->parent;
            }
        }

        return $currentObject->sid ?: $currentObject->id;
    }

    public function getRelativeImagePath()
    {
        $url = $this->getUploadUrl('image');  // берем url, а не path, потому что так проще
        $relativeUrl = preg_replace('#^/uploads/#', '', $url);
        return $relativeUrl;
    }

    public function attachImageAndSave($file, $behaviorName)
    {
        if (!$this->id) {
            throw new \Exception("Can't attach image to unsaved model");
        }

        $behavior = $this->getBehavior($behaviorName);
        $attributeName = $behavior->attribute;

        $ext = pathinfo($file)['extension'];
        $uniqueName = Yii::$app->security->generateRandomString(32);
        $this->$attributeName = $uniqueName . '.' . $ext;

        $path = $this->getUploadPath($attributeName);

        if (is_string($path) && FileHelper::createDirectory(dirname($path))) {
            if (!copy($file, $path)) {
                throw new \Exception("Failed to copy file $file to path $path");
            }
        } else {
            throw new \Exception("Directory specified in 'path' attribute doesn't exist or cannot be created.");
        }

        // Удалим старую картинку если она была.
        // Путь до нее нужно получить до save(), потому что после save()
        // мы будем получать уже путь к новой картинке.
        $path = $this->getUploadPath($attributeName, true);

        $this->ensureSave();

        if (is_file($path)) {
            echo "Deleting $path";
            unlink($path);
        }
    }

    /*
     * CRUD
     */


    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parent = parent::rules();

        unset($parent['image/length']);
        //  отключено - некорректно работает, когда sid == null
        // пусть это обрабатывает mysql сам
        unset($parent['pid,sid/unique']);

        return array_merge($parent, [
            [['sid'], 'default', 'value' => null], // for unique index
            ['image', 'file', 'extensions' => 'jpg, png, gif', 'on' => ['insert', 'update']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sid' => 'Уникальный идентификатор',
            'pid' => 'Pid',
            'name' => 'Название',
            'value' => 'Значение',
            'data' => 'Свойства',
            'image' => 'Изображение',
            'content' => 'Содержимое',
            'is_active' => 'Видимый',
            'sort' => 'Сортировка',
        ];
    }
}
