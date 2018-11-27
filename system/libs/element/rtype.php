<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 23.07.2017
 * Time: 9:29
 */

namespace system\libs\element;

if (!defined('IWEB')) { die("Error!"); }


class rtype
{
    static function int16(){
        $int = unpack("s", substr(element::$element, element::$p, 2));
        element::$p += 2;
        return $int[1];
    }

    static function int32(){
        $int = unpack("i", substr(element::$element, element::$p, 4));
        element::$p += 4;
        return $int[1];
    }

    static function int64(){
        $int = unpack("q", substr(element::$element, element::$p, 8));
        element::$p += 8;
        return $int[1];
    }

    static function float(){
        $float = unpack("f", substr(element::$element, element::$p, 4));
        element::$p += 4;
        return round($float[1],2);
    }

    static function string($len, $to="UTF-8", $from="UTF-16LE"){
        $string = trim(mb_convert_encoding(substr(element::$element, element::$p, $len), $to, $from), "\x00");
        element::$p += $len;
        return $string;
    }

    static function getValue($typeField){
        static $data;
        $value = explode(":", $typeField);
        switch ($value[0]) {
            case 'int16':
                $data = rtype::int16();
                break;

            case 'int32':
                $data = rtype::int32();
                break;

            case 'int64':
                $data = rtype::int64();
                break;

            case 'float':
                $data = rtype::float();
                break;

            case 'string':
                $data = rtype::string($value[1],"UTF-8", "GB2312");
                break;

            case 'wstring':
                $data = rtype::string($value[1]);
                break;
        }
        return $data;
    }

}