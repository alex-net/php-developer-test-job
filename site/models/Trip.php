<?php

namespace app\models;

class Trip extends \yii\db\ActiveRecord
{
    public static function getCoprorateListForOptions()
    {
        $col = static::find()->select('corporate_id')->distinct()->column();
        if ($col) {
            return array_combine($col, $col);
        }
        return [];
    }

}

