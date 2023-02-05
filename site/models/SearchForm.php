<?php

namespace app\models;

class SearchForm extends \yii\base\Model
{
    public $airPortId, $coprorateId, $serviceId;

    public function rules()
    {
        return [
            ['airPortId', 'exist', 'targetClass' => AirportName::class, 'targetAttribute' => ['airPortId' => 'airport_id']],
            ['coprorateId', 'exist', 'targetClass' => Trip::class, 'targetAttribute' => ['coprorateId' => 'corporate_id']],
            ['serviceId', 'exist', 'targetClass' => TripService::class, 'targetAttribute' => ['serviceId' => 'service_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'airPortId' => 'отправление из аэропорта',
        ];
    }

    /**
     * массив параметров для применения фильтра ...
     *
     * @param      array  $data   Входной пост запрос ..
     *
     * @return     bool    The parameters for redirect.
     */
    public function getParamsForRedirect($data)
    {
        if ($data && !$this->load($data) || !$this->validate()) {
            return false;
        }

        return array_filter($this->attributes);
    }

    /**
     * Првайдер с резульатами поиска ...
     */
    public function getSearchResult()
    {
        $attrs = array_filter($this->attributes);
        $q = Trip::find()->andFilterWhere(['trip.corporate_id' => $this->coprorateId]);
        $q->select(['trip.id', 'trip.corporate_id']);
        $q->asArray();
        $q->indexBy('id');
        if ($this->serviceId || $this->airPortId) {

          $q->leftJoin(['ts' => '{{%trip_service}}'], 'ts.trip_id = trip.id');
          $q->andFilterWhere(['ts.service_id' => $this->serviceId]);
          $q->groupBy('trip.id');

          if ($this->airPortId) {
            $q->leftJoin(['fs' => '{{%flight_segment}}' ], 'fs.flight_id = ts.id');
            $q->andWhere(['fs.depAirportId' => $this->airPortId]);
          }
        }

        $join = $q->join ?? [];

        \Yii::info($join, 'tablesUsedInFrom');

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $q,
        ]);

        $models = $dataProvider->models;

        if ($models) {
            $ids = array_keys($models);
            $servises = TripService::find()->where(['trip_id' => $ids])->groupBy('trip_id')->asArray()->select([
                'ids' => new \yii\db\Expression('group_concat(id)'),
                'services' => new \yii\db\Expression('group_concat(DISTINCT service_id)'),
                'trip_id',
            ])->all();

            $servIds = [];
            foreach ($servises as $tsItem) {
                $models[$tsItem['trip_id']]['service_id'] = $tsItem['services'];
                $models[$tsItem['trip_id']]['airos'] = null;
                $servIds = array_merge($servIds, explode(',', $tsItem['ids']));
            }

            // пробуем досать аэропорт ...
            if ($servIds) {
                $fs = FlightSegment::find()->alias('fs')->where(['fs.flight_id' => $servIds])->select(['airportId' => new \yii\db\Expression('group_concat(fs.depAirportId)') ]);
                $fs->leftJoin(['ts' => 'trip_service'], 'ts.id = fs.flight_id')->addSelect(['ts.trip_id'])->groupBy('ts.trip_id');
                $fs = $fs->asArray()->all();
                $airoIds = [];
                foreach ($fs as $fligItem) {
                    $airoIds = array_merge($airoIds, $fligItem['airportId'] ? explode(',', $fligItem['airportId']) : []);
                }
                if ($airoIds) {
                    $airoIds  = AirportName::find()->where(['airport_id' => $airoIds, 'language_id' => 137])->asArray()->select(['airport_id', 'value'])->indexBy('airport_id')->all();
                }


                foreach ($fs as $fligItem) {
                    if (!$fligItem['airportId']){
                        continue;
                    }

                    $airos = [];
                    foreach (explode(',', $fligItem['airportId']) as $airoId) {
                        if (!empty($airoIds[$airoId])) {
                            $airos[] = $airoIds[$airoId]['value'];
                        }
                    }

                    if ($airos) {
                        $models[$fligItem['trip_id']]['airos'] = implode('; ', $airos);
                    }

                    // \Yii::info($models[$fligItem['trip_id']], 'fligItem ' . $fligItem['trip_id']);



                }
               // \Yii::info($fs, '$fs');
            }

            \Yii::info($models, '$servises');
            $dataProvider->models = $models;
        }


        return $dataProvider;
    }
}