<?php

namespace system\controllers;

use system\data\config;
use system\libs\system;
use system\models\indexModel;

if (!defined('IWEB')) { die("Error!"); }

class indexContrl
{

    static function index()
    {
        system::$site_title = config::$site_title;
        $online = indexModel::onlineList();
        $account = indexModel::accountList();
        system::load("index");
        system::set("{status}", indexModel::statusServer());
        system::set("{online}", $online['count']);
        system::set("{online_users}", $online['data']);
        system::set("{accounts}", $account['count']);
        system::set("{accounts_list}", $account['data']);
        system::show('content');
        system::clear();
    }

    static function login()
    {
        system::$site_title = "登陆";
        system::load("login");
        system::show('content');
        system::clear();
    }

    static function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['id']);
        header("Location: ".config::$site_adr);
    }

}