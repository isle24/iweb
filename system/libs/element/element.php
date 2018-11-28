<?php

namespace system\libs\element;

use system\data\config;
use system\libs\database;
use system\libs\system;

if (!defined('IWEB')) {
    die("Error!");
}

class element
{

    static $element;
    static $version;
    static $version2;
    static $timestamp;
    static $data;
    static $p = 0;
    static $list_id_name_icon;

    static function get_element()
    {

        $read = fopen(config::$elementPath . "/elements.data", "r");
        while (!feof($read))
            self::$element .= fgets($read, 1000);
        fclose($read);
    }

    static function get_info()
    {
        self::get_element();
        self::$version = rtype::int16();
        self::$version2 = rtype::int16();
        self::$timestamp = rtype::int32();
    }

    static function read()
    {
        ignore_user_abort();
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        header('Connection: close');
        @ob_end_flush();
        @ob_flush();
        @flush();
        if(session_id()){
            session_write_close();
        }
        database::query("TRUNCATE TABLE items");
        self::get_info();
        element_config::get_config();
        $readErr = 0;
        $count_lists = count(element_config::$source_config);
        for ($list = 0; $list < $count_lists; $list++) {
            if ($list == 20) {
                $head = substr(self::$element, self::$p, 4);
                self::$p += 4;
                $count = substr(self::$element, self::$p, 4);
                self::$p += 4;
                $count2 = unpack("i", $count);
                $body = substr(self::$element, self::$p, $count2[1]);
                self::$p += $count2[1];
                $till = substr(self::$element, self::$p, 4);
                self::$p += 4;
            }
            if ($list == 100) {
                $head = substr(self::$element, self::$p, 4);
                self::$p += 4;
                $count = substr(self::$element, self::$p, 4);
                self::$p += 4;
                $count2 = unpack("i", $count);
                $body = substr(self::$element, self::$p, $count2[1]);
                self::$p += $count2[1];
            }
            $count_items = rtype::int32();
            for ($item = 0; $item < $count_items; $item++) {
                
                foreach (element_config::$source_config[$list][4] as $key => $value) {
                    if ($list != element_config::$dialog_list) {
                        self::$data[$list][$item][$key] = rtype::getValue($value);
                    } else {
                        self::$data[$list][$item]["ID"] = rtype::int32();
                        self::$data[$list][$item]["text"] = rtype::string(128);
                        self::$data[$list][$item]["count_win1"] = $count1 = rtype::int32();
                        for ($q = 0; $q < $count1; $q++) {
                            self::$data[$list][$item]["window_" . $q . "_id"] = rtype::int32();
                            self::$data[$list][$item]["window_" . $q . "_parent"] = rtype::int32();
                            self::$data[$list][$item]["window_" . $q . "_text_len"] = $text_len = rtype::int32();
                            self::$data[$list][$item]["window_" . $q . "_text"] = rtype::string($text_len * 2);

                            self::$data[$list][$item]["count_win2_$q"] = $count2 = rtype::int32();
                            for ($t = 0; $t < $count2; $t++) {
                                self::$data[$list][$item]["window_" . $q . "_param_$t"] = rtype::int32();
                                self::$data[$list][$item]["window_" . $q . "_text_$t"] = rtype::string(128);
                                self::$data[$list][$item]["window_" . $q . "_id_$t"] = rtype::int32();
                            }
                        }
                    }
                }

                if (isset(self::$data[$list][$item]['icon'])) {
                    $icon = str_replace("\\", "/", self::$data[$list][$item]['icon']);
                    if (!database::query("INSERT INTO items (itemID, itemName, itemIcon, itemList) VALUES ('" . self::$data[$list][$item]['ID'] . "','" . database::escape(self::$data[$list][$item]['Name']) . "','" . $icon . "', '" . $list . "')")) {
                        $readErr++;
                    }
                }
                self::$data = array();
            }

        }

        if ($readErr == 0){
            system::log("在数据库中加载项目");
            system::jms("success", "Element.data被写入数据库");
        }else
            system::jms("danger", "写入数据库时出错，错误:$readErr");

    }
}