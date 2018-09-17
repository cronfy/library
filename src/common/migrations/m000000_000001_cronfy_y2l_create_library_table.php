<?php

use yii\db\Migration;

/**
 * Handles the creation of table `library`.
 */
class m000000_000001_cronfy_y2l_create_library_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('library', [
            'id' => $this->primaryKey(),
            'sid' => $this->string(),
            /*
             * С pid ситуация такая.
             * Если сделать допустимым значением null, то не будет
             * работать constraint pid,sid|unique. Вернее, будет работать только если установлен
             * pid, а для верхнего уровня, где pid == null, работать не будет.
             * Если же сделать null недопустимым значением, то не будут работать contraints
             * для проверок foreign, так как для элементов верхнего уровня придется задавать
             * pid = 0, а такого элемента не будет существовать. Кроме того, приходится все время
             * помнить, что врехний уровень - это pid == 0, а не pid is null, что не совсем логично.
             * Хорошего решения нет, поэтому выбан такой вариант:
             * 1. pid может быть null, и топовые элементы - это элементы с pid == null. Для удобства
             * разработки и логичности.
             * 2. Проверка уникальности sid для элементов верхнего уровня работать не будет. Если такая
             * проверка требуется, ее нужно обеспечивать на уровне php.
             */
            'pid' => $this->integer()->null(),
            'name' => $this->string(),
            'value' => $this->string(),
            'data' => $this->text(),
            'image' => $this->string(),
            'content' => $this->text(),
            'is_active' => $this->boolean(),
            'sort' => $this->integer(),
        ], 'CHARACTER SET utf8 ENGINE=InnoDb');

        $this->createIndex('pid,sid|unique', 'library', ['pid', 'sid'], true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('library');
    }
}
