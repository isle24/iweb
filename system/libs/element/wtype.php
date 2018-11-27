<?php

namespace system\libs\element;

if (!defined('IWEB')) { die("Error!"); }

class wtype{

    static function int16($data){
        return pack("s", $data);
    }

    static function int32($data){
        return pack("i", $data);
    }

    static function int64($data){
        return pack("q", $data);
    }

    static function float($data){
        return pack("f", $data);
    }

    static function string($data, $len, $from="UTF-8", $to="UTF-16LE"){
        $string = mb_convert_encoding($data, $to, $from);
        $result = $len - strlen($string);
        for ($i = 0; $i < $result; $i++) $string .= "\x00";
        return $string;
    }

    static function setValue($typeField, $value){
       static $result;
        $typeField = explode(":", $typeField);
        switch ($typeField[0]) {
            case "int16":
                $result = wtype::int16($value);
                break;

            case "int32":
                $result = wtype::int32($value);
                break;

            case "int64":
                $result = wtype::int64($value);
                break;

            case "float":
                $result = wtype::float($value);
                break;

            case "string":
                $result = wtype::string($value, $typeField[1], "UTF-8", "GB2312");
                break;

            case "wstring":
                $result = wtype::string($value, $typeField[1]);
                break;
        }
        return $result;
    }
}