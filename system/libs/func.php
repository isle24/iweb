<?php

namespace system\libs;

use system\data\config;

if (!defined('IWEB')) {
    die("Error!");
}

class func
{

    static $error = "";

//
    static function readImage($nameImage)
    {
        $path = dir . "/system/data/$nameImage.";
        //$path = dir . "/iweb/system/data/$nameImage.";
        if ($data['image'] = imagecreatefrompng($path . "png")) {
            if ($file = fopen($path . 'txt', 'r')) {
                $data['imageWidth'] = (int)fgets($file);
                $data['imageHeight'] = (int)fgets($file);
                $data['rows'] = (int)fgets($file);
                $data['cols'] = (int)fgets($file);
                $data['line'] = array();
                $count = 1;
                while (!feof($file)) {
                    $data['line'][$count] = @mb_convert_encoding(trim(fgets($file)), "UTF-8", "GB2312");
                    $count++;
                }
                fclose($file);
            } else {
                self::$error = "无法读取带有图标名称的文件";
            }
        } else {
            self::$error = "无法得到照片";
        }
        return $data;
    }

    static function uploadImage($imageName)
    {
        ignore_user_abort();
        set_time_limit(0);
        header('Connection: close');
        @ob_end_flush();
        @ob_flush();
        @flush();
        if(session_id()){
            session_write_close();
        }
        database::query("TRUNCATE TABLE iconItems");
        $image = self::readImage($imageName);
        $imageTColor = imagecreatetruecolor($image['imageWidth'], $image['imageHeight']);
        $i = 0;
        foreach ($image['line'] as $number => $value) {
            if ($value == '') continue;
            if ($number > $image['cols']) $row = floor(($number - 1) / $image['cols']); else $row = 0;
            $col = $number - ($row * $image['cols']) - 1;
            if ($col < 0) $col = 0;
            imageCopy($imageTColor, $image['image'], 0, 0, $col * $image['imageWidth'], $row * $image['imageHeight'], $image['imageWidth'], $image['imageHeight']);
            ob_start();
            imagejpeg($imageTColor, null, 90);
            $result = ob_get_clean();
            $icons[$value] = array('icon' => $result);
            if (!database::query("INSERT INTO `iconItems` (`name`, `icon`) VALUES ('" . $value . "', '" . database::escape($result) . "')"))
                self::$error = "数据库查询错误!";
            $i++;
        }
        if (empty(self::$error)) {
            system::log("在数据库中加载图标");
            system::jms("success", "图标上传到数据库");
        } else
            system::jms("danger", "加载图标时出错");

    }

    static function readChat(){
        $chat = file(config::$chatFile);
        $countLine = sizeof($chat);
        $count = 0;
        $charArr = array();
        foreach (array_reverse($chat, true) as $value) {
            if ($count < 50) {
                preg_match_all("/([0-9]{0,}-[0-9]{0,}-[0-9]{0,} [0-9]{0,2}:[0-9]{0,2}:[0-9]{0,2}) ([A-z0-9]+) ([A-z0-9-]+): ([A-z0-9]{0,}) : ([A-z0-9]+): src=([0-9]+) ([A-z]+)=([0-9]+) msg=([A-z0-9+=]+)/", $value, $data);
                $msg = str_replace(array("<0>", "", "", ""), "", iconv("UTF-16LE", "UTF-8", base64_decode($data[9][0])));
                if ($data[7][0] == "fid"){
                    $clanID = $data[8][0];
                    $data[8][0] = 3;
                } else $clanID = 0;

                $charArr[$data[8][0]][] = array(
                    "data" => $data[1][0],
                    "role" =>  $data[6][0],
                    "msg" =>  $msg,
                    "clan" => $clanID
                );
            } else break;
            $count++;
        }
        return $charArr;
    }

    static function getDesk()
    {
        $deskMain = "";
        $df = fopen(dir . "/system/data/item_ext_desc.txt", "r");
       // $df = fopen("/iweb/system/data/item_ext_desc.txt", "r");
       // fgets($df);
        // fgets($df);
        // fgets($df);
        // fgets($df);
        // fgets($df);
        while (!feof($df)) {
            $line = fgets($df);
            if (!empty($line) or $line != " ") {
                $desc = explode("  ", $line, 2);
                $deskMain[$desc[0]] = (!isset($desc[1])) ? "null" : str_replace('"', "", $desc[1]);
            }
        }
        return $deskMain;
    }

    /**
 * 对数据进行编码转换
 * @param array/string $data 数组
 * @param string $output 转换后的编码
 * Created on 2016-7-13
 */
    static function array_iconv($data, $output = 'utf-8') {
  $encode_arr = array('UTF-8','ASCII','GBK','GB2312','BIG5','JIS','eucjp-win','sjis-win','EUC-JP');
  $encoded = mb_detect_encoding($data, $encode_arr);
  if (!is_array($data)) {
    return mb_convert_encoding($data, $output, $encoded);
  }
  else {
    foreach ($data as $key=>$val) {
      $key = array_iconv($key, $output);
      if(is_array($val)) {
        $data[$key] = array_iconv($val, $output);
      } else {
      $data[$key] = mb_convert_encoding($data, $output, $encoded);
      }
    }
  return $data;
  }
}


    static function reColorDesc($string)
    {
        for ($i = 0; $i < strlen($string); $i++) {
            $pos = strpos($string, "^", $i);
            if ($pos !== false) {
                $color = substr($string, $pos + 1, 6);
                $string = substr_replace($string, "<span style=\"color:#$color\">", $pos, 7);
                $string .= "</span>";
            }
        }
        $string = htmlspecialchars(str_replace('\r', "<br/>", $string));
        return $string;
    }
    
    
    static function base64EncodeImage ($image_file) {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
        }


}