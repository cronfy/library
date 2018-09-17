<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \cronfy\library\common\models\Library */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="library-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php if (@$model->getProperties()->getdefaultPropertiesOverride()['value'] !== false) : ?>
    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
    <?php endif ?>

    <?php if ($model->properties->hasProperties()) : ?>
        <h3>Свойства</h3>

        <?php foreach ($model->properties->getPropertySids() as $propertySid) : ?>
            <?php /** @var \cronfy\customProperties\GenericProperty $property */ ?>
            <?php $property = $model->properties->getProperty($propertySid); ?>
            <?php $view = $this->context->getPropertyView($property) ?>
            <?php if ($view) : ?>
                <?= $this->render($view, compact('property', 'form')) ?>
            <?php else : ?>
                <?php if (count($property->attributes()) > 1) : ?>
                    <div class="panel panel-default">
                        <div class="panel-heading"><?= $property->propertyLabel ?></div>
                        <div class="panel-body">
                            <?php foreach ($property->attributes() as $attribute) : ?>
                                <?= $form->field($property, $attribute)->textInput(['maxlength' => true]) ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <?= $form->field($property, 'value')->textInput(['maxlength' => true]) ?>
                <?php endif ?>
            <?php endif ?>

        <?php endforeach ?>
        <br>
    <?php endif ?>

    <?php if (
        !$model->properties->isReplaces('image')
        && @$model->getProperties()->getdefaultPropertiesOverride()['image'] !== false
    ) : ?>
        <?php
        // your fileinput widget for single file upload
        // http://webtips.krajee.com/advanced-upload-using-yii2-fileinput-widget/
        $configAddon = $model->image ? [
            'initialPreview' => [
                $model->getUploadUrl('image')
            ],
            'initialPreviewConfig' => [
                [
                    'size' => @filesize($model->getUploadPath('image')),
                ]
            ],
        ] : [];
        $pluginOptionsDefault = [
            'allowedFileExtensions' => ['jpg', 'gif', 'png'],

            // initial preview
            'initialPreviewAsData' => true,
            'initialPreviewShowDelete' => false,

            // live preview
            'fileActionSettings' => [
                'showDrag' => false,
                'showZoom' => true,
            ],

            // near Browse buttons
            'showUpload' => false,
            'showRemove' => false,

            // zoom modal
            // считай не настраивается :(
        ];
        $pluginOptions = array_merge($pluginOptionsDefault, $configAddon);

        echo $form->field($model, 'image')->widget(\kartik\file\FileInput::class, [
            'options' => [ 'accept' => 'image/*' ],
            'pluginOptions' => $pluginOptions
        ]);
        //    echo$form->field($model, 'cover')->fileInput()

        ?>
    <?php endif ?>

    <?php if (@$model->getProperties()->getdefaultPropertiesOverride()['content']['editor'] === 'plaintext') : ?>
        <?= $form->field($model, 'content')->textarea(['rows' => 10]) ?>
    <?php else : ?>
        <?= $form->field($model, 'content')->widget($this->context->module->htmlEditorWidgetClass) ?>
    <?php endif ?>

    <?php if (@$model->getProperties()->getdefaultPropertiesOverride()['sort'] !== false) : ?>
        <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>
    <?php endif ?>

    <?= $form->field($model, 'sid')->textInput(['maxlength' => true]) ?>

    <?php if (@$model->getProperties()->getdefaultPropertiesOverride()['is_active'] !== false) : ?>
        <?= $form->field($model, 'is_active')->checkbox() ?>
    <?php endif ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
