<?php

namespace system\models;

use system\data\config;
use system\libs\database;
use system\libs\ssh;
use system\libs\stream;
use system\libs\struct\GMRoleData;
use system\libs\system;

if (!defined('IWEB')) {
    die("Error!");
}

class serverModel
{

    public static $auth;
    public static $gacd;
    public static $gamedb;
    public static $gdeliveryd;
    public static $gfactiond;
    public static $glinkd1;
    public static $logservice;
    public static $uniquenamed;
    public static $gs;

    static function sendChatMessage($msg, $chanel)
    {
        if (!empty($msg)) {
            stream::writeByte($chanel);
            stream::writeByte(0);
            stream::writeInt32(0);
            stream::writeString($msg);
            stream::writeOctets("");
            stream::pack(0x78);
            if (stream::Send(config::$serverIP, config::$GProviderPort)) {
                system::jms("success", "消息发送");
                system::log("发送消息 聊天消息");
            } else
                system::jms("danger", "发送消息时出错");
        } else
            system::jms("info", "空消息未发送");


    }

    static function statusServer()
    {
        $dataMemFile = explode("\n", file_get_contents("/proc/meminfo"));
        $data = array();
        foreach ($dataMemFile as $line) {
            if (!empty($line)) {
                list($key, $val) = explode(":", str_replace(" kB", "", $line));
                $data[$key] = intval((int)trim($val) / 1024);
            }
        }
        return $data;
    }

    static function mail($data, $online = false)
    {
        if (!empty($data['idChar']) && is_numeric($data['idChar'])) {
            stream::$writeData = "";
            stream::writeInt32(344);
            stream::writeInt32(32);
            stream::writeByte(3);
            stream::writeInt32($data['idChar']);
            stream::writeString($data['titleItem']);
            stream::writeString($data['messageItem']);
            stream::writeInt32($data['idItem']);
            stream::writeInt32(0);
            stream::writeInt32($data['countItem']);
            stream::writeInt32($data['maxCountItem']);
            stream::writeOctets($data['octetItem']);
            stream::writeInt32($data['prototypeItem']);
            stream::writeInt32($data['timeItem']);
            stream::writeInt32(0);
            stream::writeInt32(0);
            stream::writeInt32($data['maskItem']);
            stream::writeInt32($data['moneyItem']);
            stream::pack(0x1076);
            if (stream::Send(config::$serverIP, config::$gdeliverydPort)) {
                if (!$online) {
                    system::jms("success", "发送邮件成功" . $data['idChar']);
                    system::log("发送邮件");
                } else return true;
                if (database::query("SELECT * FROM history_mail WHERE idItem='" . database::safesql($data['idItem']) . "'")) {
                    if (database::num() == 0) {
                        $insertKey = "";
                        $insertValue = "";
                        foreach ($data as $key => $value) {
                            if ($key != "idChar") {
                                $insertKey .= "$key,";
                                $insertValue .= "'" . database::safesql($value) . "',";
                            }
                        }
                        database::query("INSERT INTO history_mail (" . rtrim($insertKey, ",") . ") VALUES (" . rtrim($insertValue, ",") . ")");
                    } else {
                        $update = "";
                        foreach ($data as $key => $value) {
                            if ($key != "idChar") {
                                $update .= $key . "='" . database::safesql($value) . "',";
                            }
                        }
                        database::query("UPDATE history_mail SET " . rtrim($update, ",") . " WHERE idItem='" . database::safesql($data['idItem']) . "'");
                    }
                } else {
                    system::jms("info", "在数据库记录历史时，发送邮件出现了问题。");
                }
            } else {
                system::jms("danger", "发送邮件错误");
            }
        } else {
            system::jms("info", "请输入邮件发送人的ID");
        }
    }

    static function sendMailAllOnline($data)
    {
        $online = GMRoleData::getListOnline();
        if ($online->count > 0) {
            foreach ($online->users as $user) {
                $data['idChar'] = $user->roleid;
                self::mail($data, true);
            }
            system::log("邮局向所有人发送邮件");
            system::jms("success", "邮局发送了邮件" . $online->count . " 角色(у/ам)");
        }

    }

    static function getItemsMail($id = "")
    {
        $items = "";
        if (isset($id) && is_numeric($id)) {
            database::query("SELECT * FROM history_mail WHERE idItem='{$id}'");
            $items = json_encode(database::assoc());
        } else {
            $history = database::query("SELECT * FROM history_mail");
            for ($i = 0; $i < database::num($history); $i++) {
                $item = database::assoc($history);
                $elItem = editorModel::getItemFromElement($item['idItem']);
                $iconName = config::$site_adr . "/index.php?function=icon&name=" . $elItem['icon'];
                $items .= "<option value='" . $item['idItem'] . "' data-content='<img src=\"$iconName\"> {$item['idItem']} {$elItem['name']}'></option>";
            }

        }
        return $items;
    }

    static function checkStatusServer()
    {
        $proc = array();
        foreach (config::$server as $key => $server) {
            $program = (!isset($server['pid_name'])) ? $server['program'] : $server['pid_name'][config::$serverTypeAuth];
            $getProcess = explode("\n", trim(shell_exec("ps aux | grep -v grep | grep -i /" . $program)));
            foreach ($getProcess as $value) {
                $proc[$key]['process'][] = str_getcsv(preg_replace("/\s{2,}/", ' ', $value), " ", "", "\n");
            }
            if (!empty($proc[$key]['process'][0][0])) {
                $proc[$key]['count'] = count($proc[$key]['process']);
                $proc[$key]['status'] = "<span style='color: green'>在线</span>";
            } else {
                $proc[$key]['count'] = 0;
                $proc[$key]['status'] = "<span style='color:red;'>离线</span>";
            }

        }
        return $proc;
    }

    static function getStartedLocation()
    {
        $getProcess = explode("\n", trim(shell_exec("ps aux | grep -v grep | grep -i /" . config::$server['gs']['program'])));
        $getInst = fopen(dir . "/system/data/instance.cfg", 'r');
        $arr = array();
        while (!feof($getInst)) {
            $str = explode(" ", fgets($getInst), 2);
            $arr[$str[0]] = $str[1];
        }
        fclose($getInst);
        $Started = "";
        if (!empty($getProcess[0])) {
            foreach ($getProcess as $process) {
                $get = str_getcsv(preg_replace("/\s{2,}/", ' ', $process), " ", "", "\n");
                $getEnd = end($get);
                $Started .= "<tr>
    <td>" . $arr[$getEnd] . "</td>
    <td>" . $get[2] . "%</td>
    <td>" . $get[3] . "%</td></tr>";
            }
        }
        return $Started;

    }

    static function startServer()
    {
        $serverData = array();
        ssh::start();
        ssh::exec("sync; echo 1 > /proc/sys/vm/drop_caches");
        foreach (config::$server as $server) {
            foreach ($server as $key => $value)
                $serverData[$key] = $value;
            if ($serverData['screen']) {
                $start = "cd " . config::$serverPath . "/" . $serverData['dir'] . "; screen -dmS " . $serverData['program'] . " ./" . $serverData['program'] . " " .
                    $serverData['config'] . " > " . config::$serverPath . "/logs/" . $serverData['program'] . ".log 2>&1"; //sleep " . $serverData['sleep'];
            } else {
                $start = "cd " . config::$serverPath . "/" . $serverData['dir'] . "; ./" . $serverData['program'] . " " .
                    $serverData['config'] . " > " . config::$serverPath . "/logs/" . $serverData['program'] . ".log &";
            }
            ssh::exec($start);
        }
        ssh::exec("sync; echo 1 > /proc/sys/vm/drop_caches");
        ssh::stop();

        if (empty(ssh::$error)) {
            system::jms("success", "发送了一个命令,启动服务器");
            system::log("启动服务器");
        } else
            system::jms("danger", ssh::$error);


    }

    static function stopServer()
    {
        ssh::start();
        foreach (config::$serverStop as $stop) {
            ssh::exec("pkill -9 $stop");
        }
        ssh::exec("sync; echo 1 > /proc/sys/vm/drop_caches");
        ssh::stop();

        if (empty(ssh::$error)) {
            system::jms("success", "发送了命令,关闭服务器");
            system::log("关闭服务器");
        } else
            system::jms("danger", ssh::$error);
    }

    static function restartServer()
    {
        self::stopServer();
        self::startServer();
    }

}