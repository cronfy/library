<?php

namespace cronfy\library\backend\controllers;

use cronfy\customProperties\GenericProperty;
use cronfy\library\backend\models\LibrarySearch;
use cronfy\library\BaseModule;
use cronfy\library\common\models\Library;
use cronfy\library\common\models\LibraryRoot;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * @property BaseModule $module
 */
class LibraryController extends crud\LibraryController
{

    public function createSearchModel()
    {
        return new LibrarySearch();
    }

    public function actionIndex()
    {
        $currentPage = ($root_id = Yii::$app->request->get('root_id'))
            ? Library::findOne($root_id)
            : new LibraryRoot();

        $searchModel = $this->createSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
        $dataProvider->query->andWhere(['pid' => $currentPage->id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currentPage' => $currentPage
        ]);
    }

    /**
     * @param $property GenericProperty
     * @return mixed|string
     */
    public function getPropertyView($property)
    {
        $file = $this->module->overrideDir . '/backend/views/library/properties/' . $property->sid . '.php';
        return file_exists(Yii::getAlias($file)) ? $file : null;
    }

    /**
     * Updates an existing Library model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update'); // UploadBehavior

        $post = Yii::$app->request->post();

        if ($model->load($post)) {
            $model->getProperties()->load($post, 'Properties');

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save() && $model->getProperties()->save()) {
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw ($e);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Library model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->createModel();

        $root_id = Yii::$app->request->get('root_id');
        if ($root_id) {
            if (!$modelParent = Library::findOne(['id' => $root_id])) {
                throw new NotFoundHttpException();
            }
        } else {
            $modelParent = new LibraryRoot();
        }

        $this->module->getBusinessLogic()->setDefaultLibraryValues($model, $modelParent);

        $model->pid = $modelParent->id;

        $model->setScenario('insert'); // UploadBehavior

        $post = Yii::$app->request->post();

        if ($model->load($post)) {
            $model->getProperties()->load($post, 'Properties');

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save() && $model->getProperties()->save()) {
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw ($e);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelParent' => $modelParent
        ]);
    }
}
