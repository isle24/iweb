<?php

namespace system\models;

use system\data\config;
use system\libs\database;
use system\libs\struct\GMRoleData;
use system\libs\system;

if (!defined('IWEB')) {
    die("Error!");
}

class indexModel
{

    static function statusServer()
    {
        return (@fsockopen(config::$serverIP, config::$linkPort)) ? "<span style='color: green'>在线</span>" : "<span style='color: red'>离线</span>";
    }

    static function login($username, $password)
    {
        $is_user = false;
        if ((boolean)preg_match("#^[aA-zZ0-9\-_]+$#", $username)) {
            if ((boolean)preg_match("#^[aA-zZ0-9\-_]+$#", $password)) {
                foreach (config::$users as $key => $user) {
                    if ($username == $user['username'] && $password == $user['password']) {
                        $_SESSION['id'] = $key;
                        $_SESSION['user'] = $username;
                        system::log("登录到控制面板");
                        system::jms('reload', "");
                        $is_user = true;
                        break;
                    }
                }
                if (!$is_user) system::jms('danger', "登录错误，不正确的用户名或密码!");

            } else
                system::jms('danger', "用户名包含无效字符!");
        } else
            system::jms('danger', "密码包含无效字符!");

    }

    static function onlineList()
    {
        $online = GMRoleData::getListOnline();
        $onlineList['count'] = $online->count;
        $onlineList['data'] = "";
        if ($online->count > 0) {
            foreach ($online->users as $user) {
                $onlineList['data'] .= "    <tr>
        <td>{$user->userid}</td>
        <td>{$user->roleid}</td>
        <td>{$user->name}</td>
        <td><a class=\"badge badge-primary\" href='" . config::$site_adr . "/?controller=editor&page=xml&id={$user->roleid}'>XML</a>
            <a class=\"badge badge-success\" href='" . config::$site_adr . "/?controller=editor&id={$user->roleid}'>属性面板</a>
            <a class=\"badge badge-warning\" href='" . config::$site_adr . "/?controller=server&page=mail&id={$user->roleid}'>邮件</a>
            <a class=\"badge badge-info\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $user->roleid . ", 3)'>角色禁言</a>
            <a class=\"badge badge-light\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $user->roleid . ", 4)'>封禁角色</a>
            <a class=\"badge badge-danger\" onclick='kickRole(" . $user->roleid . ")' href='javascript:void(0)'>强踢下线</a>
        </td>
    </tr>";
            }
        }
        return $onlineList;
    }

    static function accountList()
    {
        $u = database::query("SELECT * FROM users");
        $account['count'] = database::num($u);
        $account['data'] = "";
        for ($i = 0; $i < $account['count']; $i++) {
            $user = database::assoc($u);

            $auth = database::query("SELECT * FROM auth WHERE userid='" . $user['ID'] . "'");
            if (database::num($auth) > 0) {
                $user['group'] = "<span class=\"badge badge-danger\" >GM</span>";
            } else {
                $user['group'] = "<span class=\"badge badge-success\" >玩家</span>";;
            }
            $account['data'] .= "<tr>
                                    <td>{$user['ID']}</td>
                                    <td>{$user['name']}</td>
                                    <td>{$user['email']}</td>
                                    <td>{$user['group']}</td>
                                    <td>{$user['creatime']}</td>
                                    <td><a class=\"badge badge-success\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#getChar\" onclick='getChars(" . $user['ID'] . ")'>角色</a>
                                     <a class=\"badge badge-primary\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#addCash\" onclick='addCash(" . $user['ID'] . ")'>发送元宝</a>
                                     <a class=\"badge badge-warning\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#editGM\" onclick='editGM(" . $user['ID'] . ")'>GM权限管理</a>
                                     <a class=\"badge badge-info\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $user['ID'] . ", 2)'>角色禁言</a>
                                     <a class=\"badge badge-danger\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $user['ID'] . ", 1)'>停封账号</a></td>
                                </tr>";
            //system::debug($user);
        }
        return $account;
    }

    static function gm($data, $function = "add")
    {
        $error = false;
        if ($function == "add") {
            if (database::query("DELETE FROM auth WHERE userid='{$data['id']}'")) {
                if (isset($data['params']) && count($data['params']) > 0) {
                    foreach ($data['params'] as $value) {
                        if (!database::query("INSERT INTO auth (userid, zoneid, rid) VALUES ('" . $data['id'] . "', '1', '" . $value . "')")) {
                            $error = true;
                        }
                    }
                    if (!$error) {
                        system::jms("success", "GM权限是为账户设置的 " . $data['id']);
                        system::log("修改GM账户权利 ".$data['id']);
                    }
                    else
                        system::jms("danger", "发布权利时出错1!");
                } else
                    system::jms("success", "GM权利从帐户中删除 " . $data['id']);
            } else
                system::jms("danger", "授予rights2时发生错误!");

        } else if ($function == "check") {
            database::query("SELECT * FROM auth WHERE userid='{$data}'");
            if (database::num() > 0) {
                $arr = array();
                for ($i = 0; $i < database::num(); $i++) {
                    $gm = database::assoc();
                    $arr[$i] = $gm['rid'];
                }
                echo json_encode($arr);
            } else
                echo 0;
        }
    }

}