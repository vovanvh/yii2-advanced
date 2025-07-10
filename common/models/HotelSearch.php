<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * HotelSearch represents the model behind the search form of `common\models\Hotel`.
 */
class HotelSearch extends Hotel
{
    public array $searchByHotelCategories = [];

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'zimmeranzahl'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['sterne'], 'each', 'rule' => ['in', 'range' => [1, 2, 3, 4, 5]]],
            [['pool', 'spa'], 'boolean'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            ['searchByHotelCategories', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search(array $params, ?string $formName = null): ActiveDataProvider
    {
        $query = Hotel::find()->alias('h');
        $query->where(['h.deleted_at' => null]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 6,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => [
                    'id' => [
                        'asc' => ['h.id' => SORT_ASC],
                        'desc' => ['h.id' => SORT_DESC],
                    ],
                    'name' => [
                        'asc' => ['h.name' => SORT_ASC],
                        'desc' => ['h.name' => SORT_DESC],
                    ],
                    'zimmeranzahl' => [
                        'asc' => ['h.zimmeranzahl' => SORT_ASC],
                        'desc' => ['h.zimmeranzahl' => SORT_DESC],
                    ],
                    'sterne' => [
                        'asc' => ['h.sterne' => SORT_ASC],
                        'desc' => ['h.sterne' => SORT_DESC],
                    ],
                    'pool' => [
                        'asc' => ['h.pool' => SORT_ASC],
                        'desc' => ['h.pool' => SORT_DESC],
                    ],
                    'spa' => [
                        'asc' => ['h.spa' => SORT_ASC],
                        'desc' => ['h.spa' => SORT_DESC],
                    ],
                    'created_at' => [
                        'asc' => ['h.created_at' => SORT_ASC],
                        'desc' => ['h.created_at' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        if (array_key_exists('HotelSearch', $params)) {
            if (
                array_key_exists('searchByHotelCategories', $params['HotelSearch'])
                && !is_array($params['HotelSearch']['searchByHotelCategories'])
            ) {
                $params['HotelSearch']['searchByHotelCategories'] = [];
            }
            if (
                array_key_exists('searchByHotelCategories', $params['HotelSearch'])
                && !is_array($params['HotelSearch']['searchByHotelCategories'])
            ) {
                $params['HotelSearch']['searchByHotelCategories'] = [$params['HotelSearch']['searchByHotelCategories']];
            }
        }

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'h.id' => $this->id,
            'h.name' => $this->name,
            'h.sterne' => $this->sterne,
            'h.created_at' => $this->created_at,
            'h.updated_at' => $this->updated_at,
            'h.deleted_at' => $this->deleted_at,
        ]);

        if (!empty($this->searchByHotelCategories)) {
            $query->joinWith('hotelCategories hc');
            $query->andWhere(['hc.id' => $this->searchByHotelCategories]);
            $query->groupBy('h.id');
        }

        return $dataProvider;
    }
}