<?php

use yii\db\Migration;

/**
 * Class m230204_091337_add_nge_db
 */
class m230204_091337_create_structure extends Migration
{
    const DB_TABLES = [
        'cbt' => 'db',
        'nemo_guide_etalon' => 'dbNge',
    ];
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('create database if not exists nge ');
        foreach (static::DB_TABLES as $dumpName => $db) {
            $dumpPath = Yii::getAlias('@app/migrations/dumps/' . $dumpName . '.sql');
            if (file_exists($dumpPath)) {
                $this->db = Yii::$app->get($db);
                $this->execute(file_get_contents($dumpPath));
            }
        }
        // докрутки по базам ...
        $this->db = Yii::$app->get('db');
        $this->createIndex('flight_segment-depAirportId-ind', '{{%flight_segment}}', ['depAirportId']);
        $this->createIndex('flight_segment-flight-depAirportId-ind', '{{%flight_segment}}', ['flight_id', 'depAirportId']);

        // $this->db = Yii::$app->get('dbNge');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->execute('drop database nge');
        $tbls = $this->db->createCommand('show tables')->queryColumn();
        foreach ($tbls as $tbl) {
            if ($tbl == 'migration') {
                continue;
            }
            $this->dropTable($tbl);
        }

    }
}
