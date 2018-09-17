<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\models\crud\LibrarySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $currentPage->name;

$bc = [];
foreach ($currentPage->walkUp() as $parent) {
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
<div class="library-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать элемент', ['create', 'root_id' => $currentPage->id], ['class' => 'btn btn-success']) ?>
        <?php if (!is_a($currentPage, \cronfy\library\common\models\LibraryRoot::class)) : ?>
            <?=  Html::a('Редактировать ' . $currentPage->name, ['update', 'id' => $currentPage->id], ['class' => 'btn btn-success']) ?>
        <?php endif ?>

    </p>

    <?php if ($dataProvider->count) : ?>
            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

    //            'id',
    //            'namespace',
    //            'sid',
    //            'pid',
                'name' => [
                    'attribute' => 'name',
                    'value' => function ($item) {
                        return
                        Html::a($item->name, ['', 'root_id' => $item->id]);
                    },
                    'format' => 'raw'
                ],
                 'value',
                 'is_active',
                // 'image',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    <?php else : ?>
    <p>Внутри "<?= $currentPage->name ?>" еще нет элементов.</p>
    <?php endif ?>
</div>
