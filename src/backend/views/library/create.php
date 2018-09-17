<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Library */

$this->title = 'Создать элемент в справочнике ' . $modelParent->name;

$bc = [];
foreach ($modelParent->walkUp() as $parent) {
    $bc[] = [
        'label' => $parent->name,
        'url' => ['/library/library', 'root_id' => $parent->id]
    ];
}

$this->params['breadcrumbs'] = array_merge(
    @$this->params['breadcrumbs'] ?: [],
    array_reverse($bc)
);

$this->params['breadcrumbs'][] = 'Создать';
?>
<div class="library-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
