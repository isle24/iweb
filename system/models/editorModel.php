<?php

namespace system\models;

use system\data\config;
use system\data\lang;
use system\libs\database;
use system\libs\stream;
use system\libs\struct\GMRoleData;
use system\libs\struct\GRoleData;
use system\libs\struct\roleBase;
use system\libs\struct\roleEquipment;
use system\libs\struct\roleItem;
use system\libs\struct\roleLevelUp;
use system\libs\struct\rolePocket;
use system\libs\struct\roleStatus;
use system\libs\struct\roleStorehouse;
use system\libs\struct\roleTask;
use system\libs\struct\userRole;
use system\libs\struct\userRoles;
use system\libs\system;

if (!defined('IWEB')) {
    die("Error!");
}

class editorModel
{
    static $visual;

    static function selectProp($arr, $value)
    {
        $result = "";
        foreach ($arr as $key => $val) {
            if ($value == $key) $active = " selected"; else $active = "";
            $result .= "<option value='{$key}'{$active}>$val</option>";
        }
        return $result;
    }

    static function getChar($id, $octetToInt = true)
    {
        if (!empty($id) && is_numeric($id)) {

            $role = new GRoleData();
            $role->base = new roleBase();
            $role->status = new roleStatus();
            $role->pocket = new rolePocket();
            $role->equipment = new roleEquipment();
            $role->task = new roleTask();
            $role->storehouse = new roleStorehouse();
            stream::putRead("");

            stream::writeInt32(-1);
            stream::writeInt32($id);
            stream::pack(0x1F43);
            if (stream::send(config::$serverIP, config::$dbPort)) {
                //Start read
                $opcode = stream::readCUint32();
                $len = stream::readCUint32();
                $localsid = stream::readInt32();
                $retcode = stream::readInt32();

                if ($len > 2000) {
                    $role->base = $role->getBase($octetToInt);
                    $role->status = $role->getStatus($octetToInt);
                    $role->pocket = $role->getPocket($octetToInt);
                    $role->equipment = $role->getEquipment($octetToInt);
                    $role->storehouse = $role->getStorehouse($octetToInt);
                    $role->task = $role->getTask($octetToInt);
                    return $role;
                } else
                    return false;
            } else
                return false;
        } else
            return false;
    }

    static function putChar($id, $data, $octetToData = true)
    {
        if (!empty($id) or is_numeric($id)) {

            if (!empty($data)) {

                $role = new GRoleData();
                stream::writeInt32(-1);
                stream::writeInt32($id);
                stream::writeByte(1);
                $role->putBase($data, $octetToData);
                $role->putStatus($data, $octetToData);
                $role->putPocket($data, $octetToData);
                $role->putEquipment($data, $octetToData);
                $role->putStorehouse($data, $octetToData);
                $role->putTask($data, $octetToData);
                stream::pack(0x1F42);

                if (stream::Send(config::$serverIP, config::$dbPort)) {
                    system::jms("success", "保存的字符");
                } else
                    system::jms("danger", "保存错误");
            } else
                system::jms("info", "空字符不能保存");
        } else
            system::jms("info", "不是真的或空的ID");
    }

    static function saveVisual($data)
    {
        if ($role = self::getChar($data['id'], false)) {
            foreach ($data['visual'] as $key => $value) {
                $key = str_replace("visual[", "", $key);
                $key = explode("-", $key);
                $name = $key[0];
                $param = $key[1];
                if (isset($key[2])) {
                    $param2 = $key[2];
                    $role->$name->$param->$param2 = $value;
                    if (isset($key[3])) {
                        $param3 = $key[3];
                        $role->$name->$param->$param2->$param3 = $value;
                    }
                } else {
                    if ($param == "items") {
                        $get = json_decode($value, true);
                        $item = null;
                        foreach ($get as $key => $vl){
                            if ($vl['id'] != 0) {
                                $item[$key] = new roleItem();
                                $item[$key]->id = $vl['id'];
                                $item[$key]->pos = $vl['pos'];
                                $item[$key]->count = $vl['count'];
                                $item[$key]->max_count = $vl['max_count'];
                                $item[$key]->data =@pack("H*", $vl['data']);
                                $item[$key]->proctype = $vl['proctype'];
                                $item[$key]->expire_date = $vl['expire_date'];
                                $item[$key]->guid1 = $vl['guid1'];
                                $item[$key]->guid2 = $vl['guid2'];
                                $item[$key]->mask = $vl['mask'];
                            }
                        }
                        $role->$name->$param = $item;
                    } else
                        $role->$name->$param = $value;
                }
            }
            //system::debug($role);

            self::putChar($data['id'], $role, false);
            //system::log("角色通过可视化编辑器进行更改 " . $data['id']);

        } else
            system::jms("danger", "错误获取字符");
    }

    static function levelUp($id, $level)
    {
        $role = self::getChar($id);
        if ($role) {
            if (is_numeric($id) && !empty($id)) {
                if ($level >= 1) {
                    $levelUP = new roleLevelUp();

                    $role->status->pp = $level * 5;
                    $role->status->property->vitality = 5;
                    $role->status->property->energy = 5;
                    $role->status->property->strength = 5;
                    $role->status->property->agility = 5;

                    $levelUP->levelProrepty($role->base->cls, $level, $role->status->property);
                    $role->status->level = $level;
                    $role->status->exp = 0;
                    if ($role->status->hp < $role->status->property->max_hp) $role->status->hp = $role->status->property->max_hp;
                    if ($role->status->mp < $role->status->property->max_mp) $role->status->mp = $role->status->property->max_mp;

                    self::putChar($id, $role);
                    system::log("改变了人物等级 " . $id);
                } else
                    system::jms("info", "等级小于1或未指定");
            } else
                system::jms("info", lang::$notValidCharID);
        } else
            system::jms("danger", "错误获取字符");
    }

    static function teleportGD($roleID)
    {
        if (!empty($roleID) && is_numeric($roleID)) {
            if ($role = self::getChar($roleID)) {
                $role->status->posx = 1284.897;
                $role->status->posy = 219.618;
                $role->status->posz = 1130.428;
                $role->status->worldtag = 1;
                self::putChar($roleID, $role);
                system::log("强行被传送到大地图的 $roleID 到到世界大地图");
            } else
                system::jms("danger", "错误获取字符");
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    static function nullSpEp($roleID)
    {
        if (!empty($roleID) && is_numeric($roleID)) {
            if ($role = self::getChar($roleID)) {
                $role->status->sp = 0;
                $role->status->exp = 0;
                self::putChar($roleID, $role);
                system::log("调整元神和经验 " . $roleID);
            } else
                system::jms("danger", "错误获取字符");
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    static function addGold($roleID, $count)
    {
        if (!empty($roleID) && is_numeric($roleID)) {
            stream::writeInt32($roleID);
            stream::writeInt32($count);
            stream::pack(0x209);
            if (stream::Send(config::$serverIP, config::$dbPort, false, false)) {
                system::jms("success“，”成功发送元宝“");
                system::log("发送元宝$计数帐户 " . $roleID);
            } else {
                system::jms("danger", "元宝没有发送成功，服务器可能出错了!");
            }
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    static function deleteRole($id)
    {
        if (!empty($id) && is_numeric($id)) {
            stream::writeInt32($id, true, -1);
            stream::writeByte(0);
            stream::pack(0xBC0);
            if (stream::Send(config::$serverIP, config::$dbPort, false, false)) {
                system::jms("success", "字符已删除");
                system::log("删除一个字符 " . $id);
            } else
                system::jms("danger", "无法删除字符");
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    static function renameRole($id, $oldName, $newName)
    {
        if (!empty($id) && is_numeric($id)) {
            stream::writeInt32($id, true, -1);
            stream::writeString($oldName);
            stream::writeString($newName);
            stream::pack(0xD4C);
            if (stream::Send(config::$serverIP, config::$dbPort, false, false)) {
                system::jms("success", "角色被重新命名");
                system::log("重命名一个字符 $id с $oldName на $newName");
            } else
                system::jms("danger", "无法重命名该字符");
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    static function charsList($id)
    {
        if (!empty($id) && is_numeric($id)) {
            $role = new GRoleData();
            $list = $role->getListRoles($id);
            //var_dump($list);die;
            $roleList = "<table>";
            if ($list->count > 0) {
                foreach ($list->roles as $role) {
                    $roleList .= "<tr>  <td>" . $role->id . "&nbsp;&blacktriangleright;&nbsp;</td> <td><b> " . $role->name . "&nbsp;&nbsp;&nbsp; </b> </td>
<td><a class=\"badge badge-primary\" href='" . config::$site_adr . "/?controller=editor&page=xml&id=" . $role->id . "'>XML</a> 
<a class=\"badge badge-success\" href='" . config::$site_adr . "/?controller=editor&id=" . $role->id . "'>编辑</a> 
<a class=\"badge badge-warning\" href='" . config::$site_adr . "/?controller=server&page=mail&id=" . $role->id . "'>发送邮件</a>
<a class=\"badge badge-info\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $role->id . ", 3)'>角色禁言</a>
<a class=\"badge badge-light\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $role->id . ", 4)'>封禁角色</a>
<a class=\"badge badge-danger\" href='javascript:void(0)' onclick='goDelChar(" . $role->id . ")'>删除</a></td></tr>";
                }
                $roleList .= "</table>";
                echo $roleList;
            } else
                echo "<p class=\"alert alert-info\">字符未找到或无法从服务器获取数据</p>";
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    static function getItemFromElement($id)
    {
        if ($item = database::squery("SELECT * FROM items WHERE itemID='$id'")) {
            $iconNameArr = explode("/", $item['itemIcon']);
            $item['name'] = $item['itemName'];
            $item['list'] = $item['itemList'];
            $item['icon'] = end($iconNameArr);
        } else {
            $item['name'] = "未知的";
            $item['list'] = 999;
            $item['icon'] = "unknown.dds";
        }
        return $item;
    }

    static function ban($id, $time, $type, $reason)
    {
        stream::writeInt32(-1);
        stream::writeInt32(0);
        stream::writeInt32($id);
        stream::writeInt32($time);
        stream::writeString($reason);
        switch ($type) {
            case 1:
                $msg = "账户 $id 已被封禁 $time 秒";
                stream::pack(0x162); // бан акка
                break;
            case 2:
                $msg = "角色 $id 的账户已被禁言 $time 秒";
                stream::pack(0x164); // бан чата акка
                break;
            case 3:
                $msg = "角色 $id 已被禁言 $time 秒";
                stream::pack(0x16A); // бан чата персонажа
                break;
            case 4:
                $msg = "角色 $id 已被封禁 $time 秒";
                stream::pack(0x168); // бан персонажа
                break;
            default:
                $msg = "错误的封禁类型";
                break;
        }
        if (stream::Send(config::$serverIP, config::$gdeliverydPort)) {
            system::log($msg);
            system::jms("success", $msg);
        } else
            system::jms("danger", "错误阻止字符/帐户");
    }

}