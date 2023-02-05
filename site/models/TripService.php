<?php

namespace app\models;

class TripService extends \yii\db\ActiveRecord
{
    public static function getServicesListForOptions()
    {
        $col = static::find()->select('service_id')->distinct()->column();
        if ($col) {
            return array_combine($col, $col);
        }
        return [];
    }
}