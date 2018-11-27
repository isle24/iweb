<?php

namespace system\models;

use system\libs\database;

class settingsModel
{


    static function getLogs()
    {
        if ($get = database::query("SELECT * FROM panel_logs ORDER BY id DESC LIMIT 15")) {
            $log = array();
            for ($i = 0; database::num($get) > $i; $i++) {
                $log[$i] = database::assoc($get);
            }
            return $log;
        } else return false;
    }


}