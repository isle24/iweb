<?php

namespace system\libs\element;


if (!defined('IWEB')) { die("Error!"); }

class element_config
{
    static $lists;
    static $dialog_list;
    static $source_config = array();

    static function get_config()
    {
        $t[] = "";
        $read = fopen(dir . "/system/data/element_configs/" . element::$version . ".cfg", "r");
      //  $read = fopen("/iweb/system/data/element_configs/" . element::$version . ".cfg", "r");
        self::$lists = fgets($read);
        self::$dialog_list = fgets($read);
        while (!feof($read)) {
            $string = trim(fgets($read));
            if ($string != "")
                self::$source_config[] = $string;
        }
        fclose($read);

        if (!empty(self::$source_config))
            self::parser_config();
    }

    static function parser_config()
    {
        $arrayResult[] = array();
        self::$source_config = array_chunk(self::$source_config, 4);
        $list_number = 0;
        foreach (self::$source_config as $array) {
            $arrayName = explode(";", $array[2]);
            $arrayType = explode(";", $array[3]);
            $arrayCount = count($arrayName);
            for ($i = 0; $i < $arrayCount; $i++) {
                if ($arrayName[$i] == "Name" or $arrayName[$i] == "ID" or $arrayName[$i] == "icon")
                    $arrayResult[$list_number][$arrayName[$i]] = $arrayType[$i];
                else
                    if(isset($arrayType[$i]))
                    $arrayResult[$list_number][$arrayName[$i] . "___" . $i] = $arrayType[$i];
            }
            self::$source_config[$list_number][] = $arrayResult[$list_number];
           $list_number++;
        }
}

}