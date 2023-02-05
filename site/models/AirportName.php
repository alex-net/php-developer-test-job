<?php 

namespace app\models;

use Yii;

class AirportName extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get('dbNge');
    }

    public static function getSuggestByName($name)
    {
        if (!$name) {
            return [];
        }
        return static::find()->where(['like', 'value', $name])->asArray()->limit(10)->select(['id' => 'airport_id', 'text' => 'value'])->all();
    }

    public static function getAiroportNameById($airoId)
    {
        $item = static::find()->where(['airport_id' => $airoId])->one();
        if ($item) {
            return $item->value;
        }
    }

}