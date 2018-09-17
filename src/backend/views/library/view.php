<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model cronfy\library\common\models\Library */

$this->title = $model->name;

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

?>
<div class="library-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'sid',
            'pid',
            'name',
            'value',
            'data:ntext',
            'image',
            'content:ntext',
            'is_active',
            'sort',
        ],
    ]) ?>

</div>
