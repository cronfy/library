<?php

namespace cronfy\library\backend\models;

use cronfy\library\common\models\Library;
use yii\data\ActiveDataProvider;

class LibrarySearch extends crud\LibrarySearch
{

    /**
     * Единственная цель - убрать фильтр по data, который теперь не строка,
     * а JsonField, и yii генерирует по нему WHERE data LIKE '%%', что приводит пустому
     * результату поиска.
     *
     * @inheritdoc
     */
    public function search($params)
    {
        $query = Library::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'pid' => $this->pid,
            'is_active' => $this->is_active,
            'sort' => $this->sort,
        ]);

        $query->andFilterWhere(['like', 'sid', $this->sid])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'value', $this->value])
//            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
