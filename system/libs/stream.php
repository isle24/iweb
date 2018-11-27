<?php

namespace system\libs;
if (!defined('IWEB')) {
    die("Error!");
}

class stream
{

    static $readData = "";
    static $readData_copy = "";
    static $writeData = "";
    static $writeData_copy = "";
    static $p = 0;
    static $p_copy = 0;


    //Manager Function

    static function putRead($data, $p = 0)
    {
        self::$readData_copy = self::$readData;
        self::$readData = $data;
        self::$p_copy = self::$p;
        self::$p = $p;
    }

    static function putWrite($data)
    {
        self::$writeData_copy = self::$writeData;
        self::$writeData = $data;
    }

    static function cuint($data)
    {
        if ($data <= 127)
            return pack("C", $data);
        if ($data < 16384)
            return pack("n", $data | 32768);
        if ($data < 536870912)
            return pack("N", $data | 3221225472);
        return pack("c", -32) . pack("N", $data);
    }

    static function pack($id)
    {
        self::$writeData = self::cuint($id) . self::cuint(strlen(self::$writeData)) . self::$writeData;
    }

    static function Send($address, $port, $firstRead = false, $read = true)
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (@socket_connect($socket, $address, $port)) {
            socket_set_block($socket);

            if ($firstRead) socket_recv($socket, self::$readData, 131072, 0);
            socket_send($socket, self::$writeData, 131072, 0);
            if ($read) socket_recv($socket, self::$readData, 131072, 0);
            socket_set_nonblock($socket);
            socket_close($socket);
            self::$writeData = "";
            return true;
        } else
            return false;
    }

    //Read Function

    static function readInt16($big = true)
    {
        if ($big == true)
            $result = unpack("n", substr(self::$readData, self::$p, 2));
        else
            $result = unpack("v", substr(self::$readData, self::$p, 2));

        self::$p += 2;
        return $result[1];
    }

    static function readInt32($big = true)
    {
        if ($big) {
            $result = unpack("i", strrev(substr(self::$readData, self::$p, 4)));
        } else {
            $result = unpack("i", substr(self::$readData, self::$p, 4));
        }

        self::$p += 4;
        return $result[1];
    }

    static function readByte()
    {
        $result = unpack("C", substr(self::$readData, self::$p, 1));
        self::$p++;

        return $result[1];
    }

    static function readOctets($toInt = false)
    {
        $size = self::readCUint32();
        if ($toInt) {
            $result = bin2hex(substr(self::$readData, self::$p, $size));
        } else
            $result = substr(self::$readData, self::$p, $size);
        self::$p += $size;
        return $result;
    }

    static function readCUint32()
    {
        $byte = self::ReadByte();

        self::$p -= 1;
        switch ($byte & 224) {
            case 224:
                self::ReadByte();
                return self::ReadInt32();
            case 192:
                return self::ReadInt32() & 1073741823;
            case 128:
            case 160:
                return self::ReadInt16() & 32767;
        }
        return self::ReadByte();
    }

    static function readString()
    {
        $size = self::readCUint32();
        $result = self::$readData;

        $result = substr(self::$readData, self::$p, $size);
        self::$p += $size;
        //var_dump($result);die;
        $result = iconv("UTF-16LE", "UTF-8", $result);
       // $result = stream::array_iconv($result);
        return $result;
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


    static function readSingle($big = true)
    {
        if ($big == true)
            $result = unpack("f", strrev(substr(self::$readData, self::$p, 4)));
        else
            $result = unpack("f", substr(self::$readData, self::$p, 4));

        self::$p += 4;
        return $result[1];
    }

    //Write Function

    static function writeInt16($data, $big = true)
    {
        if ($big == true)
            self::$writeData .= pack("n", $data);
        else
            self::$writeData .= pack("v", $data);

    }

    static function writeInt32($data, $big = true, $arg1 = false, $arg2 = false)
    {
        if ($big == true) {
            if ($arg1!==false && $arg2===false)
                self::$writeData .= pack("N*", $arg1, $data);
            else if ($arg1!==false && $arg2!==false)
                self::$writeData .= pack("N*", $arg1, $arg2, $data);
            else
                self::$writeData .= pack("N*", $data);
        } else
            self::$writeData .= pack("V*", $data);
    }

    static function writeByte($data)
    {
        self::$writeData .= pack("C", $data);
    }

    static function writeOctets($data, $toData = false)
    {
        if ($toData) {
            $pack = @pack("H*", $data);
            self::$writeData .= self::cuint(strlen($pack)) . $pack;
        } else {
            self::$writeData .= self::cuint(strlen($data)) . $data;
        }
    }

    static function writeCUint32($data, $big = true)
    {
        if ($data <= 127)
            self::writeByte($data);
        else {
            if ($data < 16384)
                self::WriteInt16($data | 32768, $big);
            else
                if ($data < 536870912)
                    self::WriteInt32($data | 3221225472);
        }
    }

    static function writeString($data)
    {
        $result = iconv("UTF-8", "UTF-16LE", $data);
        self::$writeData .= self::cuint(strlen($result)) . $result;
    }

    static function writeSingle($data, $big = true)
    {
        if ($big == true)
            self::$writeData .= strrev(pack("f", $data));
        else
            self::$writeData .= pack("f", $data);
    }

}