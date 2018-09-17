<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Library */

$this->title = 'Редактирование: ' . $model->name;

$bc = [];
foreach ($model->walkUp() as $parent) {
    $bc[] = [
        'label' => $parent->name,
        'url' => ['/library/library', 'root_id' => $parent->id]
    ];
}

$this->params['breadcrumbs'] = array_merge(
    @$this->params['breadcrumbs'] ?: [],
    array_reverse($bc)
);

$this->params['breadcrumbs'][] = 'Редактирование';

?>
<div class="library-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
