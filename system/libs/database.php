<?php

namespace system\libs;

use mysqli;
use system\data\config;

if (!defined('IWEB')) { die("Error!"); }
class database
{

    private static $connect;
    static $mysqlError;
    static $queryID;
    static $queryNUM = 0;

    public function __construct()
    {
        self::Run();
    }

    private static function Run()
    {
        self::$connect = @new mysqli(config::$db_host, config::$db_user, config::$db_password, config::$db_table);
        if (self::$connect->connect_errno) {
            printf("Connect failed: %s\n", self::$connect->connect_error);
            exit();
        } else {
            self::$connect->query("SET NAMES " . config::$db_charset);
            return self::$connect;
        }
    }

    public static function squery($value, $cycle = 0)
    {
        if ($cycle == 0) {
            $data = self::assoc(self::query($value));
            self::free();
            return $data;
        } else {
            $data = self::query($value);
            $rows = array();
            while ($row = self::assoc($data))
                $rows[] = $row;
            self::free();
            return $rows;
        }
    }

    public static function assoc($value = "")
    {
        if (empty($value))
            return mysqli_fetch_assoc(self::$queryID);
        else return mysqli_fetch_assoc($value);
    }

    public static function query($value)
    {
        self::$queryID = self::Run()->query($value);
        if (self::$queryID) {
            self::$queryNUM++;
            return self::$queryID;
        } else
            return false;
    }

    public static function free($value = "")
    {
        if (empty($value)) $value = self::$queryID;
        @mysqli_free_result($value);
    }

    public static function fetch($value = "")
    {
        if (empty($value)) $value = self::$queryID;
        return mysqli_fetch_array($value);
    }


    public static function num($value = "")
    {
        if (empty($value)) {

            if (!empty(self::$queryID)) {
                $value = self::$queryID;
                return mysqli_num_rows($value);
            } else {
                return 0;
            }
        } else {
            return mysqli_num_rows($value);
        }

    }

    public static function safesql($value)
    {
        return self::Run()->real_escape_string($value);
    }

    public static function escape($value)
    {
        return self::Run()->escape_string($value);
    }

    public static function clear()
    {
        self::$queryNUM = "";
        self::$queryID = "";
    }

    function __destruct()
    {
        self::Run()->close();
        self::clear();
    }


}