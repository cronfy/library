<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 10.07.18
 * Time: 18:39
 */

namespace cronfy\library\common\models;

/**
 * Дублируем здесь CustomProperties, чтобы внешняя зависимость шла не из BaseModule,
 * а отсюда, и чтобы понятие класса CustomProperties в принципе существовало внутри модуля.
 * Иначе сложно догадаться, откуда они берутся, приходится искать по всему коду, пока не
 * найдешь в BaseModule, а это неочевидно.
 *
 * Да и методы кое-какие надо добавить.
 */
class CustomProperties extends \cronfy\customProperties\CustomProperties
{
    public function getdefaultPropertiesOverride() {
        /*
        // for example
        $override = [
            'content' => [
                'editor' => 'plaintext',
            ],
            'is_active' => false,
        ];

        return $override;
        */

        return [];
    }

}