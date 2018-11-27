<?php

namespace system\controllers;

use system\data\config;
use system\libs\system;
use system\models\serverModel;

if (!defined('IWEB')) {
    die("Error!");
}

class serverContrl
{

    static function index()
    {
        system::$site_title = "服务器管理";

        $statusLocation = serverModel::getStartedLocation();
        $statusProcess = serverModel::checkStatusServer();
        $status = serverModel::statusServer();
        $useMem = $status['MemTotal'] - $status['MemFree'];
        $useSwap = $status['SwapTotal'] - $status['SwapFree'];

        system::load("server");
        system::set("{auth}", $statusProcess['auth']['status']);
        system::set("{gacd}", $statusProcess['gacd']['status']);
        system::set("{gdeliveryd}", $statusProcess['gdeliveryd']['status']);
        system::set("{gameddb}", $statusProcess['gamedbd']['status']);
        system::set("{gfactiond}", $statusProcess['gfactiond']['status']);
        system::set("{glinkd}", $statusProcess['glinkd']['status']);
        system::set("{logservice}", $statusProcess['logservice']['status']);
        system::set("{uniquenamed}", $statusProcess['uniquenamed']['status']);
        system::set("{gs}", $statusProcess['gs']['status']);

        system::set("{auth_count}", @$statusProcess['auth']['count']);
        system::set("{gacd_count}", $statusProcess['gacd']['count']);
        system::set("{gdeliveryd_count}", $statusProcess['gdeliveryd']['count']);
        system::set("{gameddb_count}", $statusProcess['gamedbd']['count']);
        system::set("{gfactiond_count}", $statusProcess['gfactiond']['count']);
        system::set("{glinkd_count}", $statusProcess['glinkd']['count']);
        system::set("{logservice_count}", $statusProcess['logservice']['count']);
        system::set("{uniquenamed_count}", $statusProcess['uniquenamed']['count']);
        system::set("{gs_count}", $statusProcess['gs']['count']);

        system::set("{auth_cpu}", (isset($statusProcess['auth']['process'][0][2])) ? $statusProcess['auth']['process'][0][2] : 0);
        system::set("{gacd_cpu}", (isset($statusProcess['gacd']['process'][0][2])) ? $statusProcess['gacd']['process'][0][2] : 0);
        system::set("{gdeliveryd_cpu}", (isset($statusProcess['gdeliveryd']['process'][0][2])) ? $statusProcess['gdeliveryd']['process'][0][2] : 0);
        system::set("{gameddb_cpu}", (isset($statusProcess['gamedbd']['process'][0][2])) ? $statusProcess['gamedbd']['process'][0][2] : 0);
        system::set("{gfactiond_cpu}", (isset($statusProcess['gfactiond']['process'][0][2])) ? $statusProcess['gfactiond']['process'][0][2] : 0);
        system::set("{glinkd_cpu}", (isset($statusProcess['glinkd']['process'][0][2])) ? $statusProcess['glinkd']['process'][0][2] : 0);
        system::set("{logservice_cpu}", (isset($statusProcess['logservice']['process'][0][2])) ? $statusProcess['logservice']['process'][0][2] : 0);
        system::set("{uniquenamed_cpu}", (isset($statusProcess['uniquenamed']['process'][0][2])) ? $statusProcess['uniquenamed']['process'][0][2] : 0);
        system::set("{gs_cpu}", (isset($statusProcess['gs']['process'][0][2])) ? $statusProcess['gs']['process'][0][2] : 0);

        system::set("{auth_mem}", (isset($statusProcess['auth']['process'][0][3])) ? $statusProcess['auth']['process'][0][3] : 0);
        system::set("{gacd_mem}", (isset($statusProcess['gacd']['process'][0][3])) ? $statusProcess['gacd']['process'][0][3] : 0);
        system::set("{gdeliveryd_mem}", (isset($statusProcess['gdeliveryd']['process'][0][3])) ? $statusProcess['gdeliveryd']['process'][0][3] : 0);
        system::set("{gameddb_mem}", (isset($statusProcess['gamedbd']['process'][0][3])) ? $statusProcess['gamedbd']['process'][0][3] : 0);
        system::set("{gfactiond_mem}", (isset($statusProcess['gfactiond']['process'][0][3])) ? $statusProcess['gfactiond']['process'][0][3] : 0);
        system::set("{glinkd_mem}", (isset($statusProcess['glinkd']['process'][0][3])) ? $statusProcess['glinkd']['process'][0][3] : 0);
        system::set("{logservice_mem}", (isset($statusProcess['logservice']['process'][0][3])) ? $statusProcess['logservice']['process'][0][3] : 0);
        system::set("{uniquenamed_mem}", (isset($statusProcess['uniquenamed']['process'][0][3])) ? $statusProcess['uniquenamed']['process'][0][3] : 0);
        system::set("{gs_mem}", (isset($statusProcess['gs']['process'][0][3])) ? $statusProcess['gs']['process'][0][3] : 0);

        system::set("{listStarted}", $statusLocation);
        system::set("{all}", $status['MemTotal']);
        system::set("{use}", $useMem);
        system::set("{free}", $status['MemFree']);
        system::set("{use_proc}", round($useMem / $status['MemTotal'] * 100));
        system::set("{swap_all}", $status['SwapTotal']);
        system::set("{swap_use}", $useSwap);
        system::set("{swap_free}", $status['SwapFree']);
        system::set("{swap_use_proc}", round($useSwap / $status['SwapTotal'] * 100));
        system::show('content');
        system::clear();
    }

    static function mail()
    {
        system::$site_title = "发送邮件";

        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['xml_edit'] == true) {

            system::load("mail");
            system::set("{idChar}", (isset($_GET['id'])) ? $_GET['id'] : "");
            system::set("{titleMail}", config::$titleMail);
            system::set("{messageMail}", config::$messageMail);
            system::set("{sendingItems}", serverModel::getItemsMail());
            system::show('content');
            system::clear();
        } else system::info("没有访问权限", "您无权访问此功能!");

    }

    static function chat()
    {
        system::$site_title = "游戏聊天";
        system::load("chat");
        system::show('content');
        system::clear();
    }


}