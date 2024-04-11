<?php

namespace pantera\helpdesk\models;

use yii\data\ActiveDataProvider;
use Yii;

class TicketsSearch extends Tickets
{
    public $withComments;
    public $withoutResponse;
    public $responsed;
    public $important;
    public $archive;

    public function rules()
    {
        return [
          [['withComments', 'withoutResponse', 'responsed', 'important', 'archive'], 'integer']
        ];
    }

    public function search($params)
    {
        $this->load($params);

        $query = Tickets::find();

        if (empty($params)) {
            $this->withoutResponse = 1;
        }

        if ($this->withComments) {
            $query->andWhere(['AND', ['!=', 'comment', ''], ['IS NOT', 'comment', null]]);
        }

        if ($this->withoutResponse) {
            $query->andWhere(['status' => self::STATUS_UPDATED_BY_USER]);
        } elseif ($this->responsed) {
            $query->andWhere(['status' => self::STATUS_UPDATED_BY_ADMIN]);
        }

        if ($this->important) {
            $query->andWhere(['important' => 1]);
        }

        if ($this->archive) {
            $query->andWhere(['status' => self::STATUS_CLOSED]);
        }

        $query->orderBy('id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}
