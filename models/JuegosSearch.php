<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Juegos;

/**
 * JuegosSearch represents the model behind the search form of `app\models\Juegos`.
 */
class JuegosSearch extends Juegos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'genero_id'], 'integer'],
            [['titulo', 'flanzamiento', 'imagen', 'created_at', 'genero.denom'], 'safe'],
            [['precio'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['genero.denom']);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Juegos::find()->joinWith('genero g');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['genero.denom'] = [
            'asc' => ['g.denom' => SORT_ASC],
            'desc' => ['g.denom' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'genero_id' => $this->getAttribute('genero.denom'),
            'flanzamiento' => $this->flanzamiento,
            'precio' => $this->precio,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'titulo', $this->titulo])
            ->andFilterWhere(['ilike', 'imagen', $this->imagen]);

        return $dataProvider;
    }
}
