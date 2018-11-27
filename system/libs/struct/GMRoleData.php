<?php

namespace system\libs\struct;

use system\data\config;
use system\libs\stream;
use system\libs\system;
if (!defined('IWEB')) { die("Error!"); }
class GMRoleData
{

    static function getListOnline()
    {
        $gmRole = new GMRoleOnline();

        stream::writeInt32(0);
        stream::writeInt32(0);
        stream::writeInt32(0);
        stream::writeOctets("");
        stream::pack(0x160);
        if (stream::Send(config::$serverIP, config::$gdeliverydPort, true)) {
            $gmRole->type = stream::readCUint32();
            $gmRole->answlen = stream::readCUint32();
            $gmRole->retcode = stream::readInt32();
            $gmRole->gmroleid = stream::readInt32();
            $gmRole->localsid = stream::readInt32();
            $gmRole->handler = stream::readInt32();
            $gmRole->count = stream::readCUint32();
            $gmRole->users = array();
            if ($gmRole->count > 0) {
                for ($i = 0; $i < $gmRole->count; $i++) {
                    $gmRole->users[$i] = new GMRoleInfo();
                    $gmRole->users[$i]->userid = stream::readInt32();
                    $gmRole->users[$i]->roleid = stream::readInt32();
                    $gmRole->users[$i]->linkid = stream::readInt32();
                    $gmRole->users[$i]->localsid = stream::readInt32();
                    $gmRole->users[$i]->gsid = stream::readInt32();
                    $gmRole->users[$i]->status = stream::readByte();
                    $gmRole->users[$i]->name = stream::readString();
                }
            }

        }
        return $gmRole;
    }

    static function kickUser($role, $time = "1", $reason = "GM", $gm = 32)
    {
        stream::writeInt32($gm);
        stream::writeInt32(1);
        stream::writeInt32($role);
        stream::writeInt32($time);
        stream::writeString($reason);
        stream::pack(0x168);
        if (stream::Send(config::$serverIP, config::$gdeliverydPort)){
            system::jms('success', "角色字符与服务器断开连接！");
        }else
            system::jms('error', "连接到服务器时出错！");

    }


}