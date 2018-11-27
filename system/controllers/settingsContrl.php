<?php

namespace system\controllers;

use system\data\config;
use system\libs\database;
use system\libs\system;
use system\models\settingsModel;

if (!defined('IWEB')) {
    die("Error!");
}

class settingsContrl
{

    static function index()
    {
        system::$site_title = "设置";
        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['settings'] == true) {

            $items = database::query("SELECT * FROM items");
            $icons = database::query("SELECT * FROM iconItems");

            if (file_exists(dir . "/system/data/iconlist_ivtrm.png"))
                $statusPngIcons = "<span style='color: green'>会发现</span>";
            else $statusPngIcons = "<span style='color: red'>不会找到</span>";

            if (file_exists(dir . "/system/data/iconlist_ivtrm.txt"))
                $statusTxtIcons = "<span style='color: green'>会发现</span>";
            else $statusTxtIcons = "<span style='color: red'>不会找到т</span>";

            system::load("settings");
            system::set("{items_count}", database::num($items));
            system::set("{icons_count}", database::num($icons));
            system::set("{status_png_icons}", $statusPngIcons);
            system::set("{status_txt_icons}", $statusTxtIcons);
            system::show("content");
            system::clear();
        } else system::info("没有访问权限，“”您无权访问此功能");
    }

    static function logs()
    {
        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['logs'] == true) {

           $logs = "";
           // system::debug(settingsModel::getLogs());
           foreach (settingsModel::getLogs() as $key => $log){
               $logs .= "<tr>";
               $logs .= "<td>".date("d.m.y / H:i",$log['date'])."</td>";
               $logs .= "<td>{$log['ip']}</td>";
               $logs .= "<td>{$log['user']}</td>";
               $logs .= "<td>{$log['action']}</td>";
               $logs .= "</tr>";
           }

            system::load('logs');
            system::set("{logs}", $logs);
            system::show('content');
            system::clear();
        } else system::info("没有访问权限，“”您无权访问此功能");

    }

}