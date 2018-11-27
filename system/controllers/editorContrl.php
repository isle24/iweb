<?php

namespace system\controllers;

use system\data\lang;
use system\libs\ArrayToXml;
use system\data\config;
use system\libs\func;
use system\libs\stream;
use system\libs\struct\roleSkill;
use system\libs\struct\roleSkills;
use system\libs\system;
use system\models\editorModel;

if (!defined('IWEB')) {
    die("Error!");
}

class editorContrl
{

    static function index()
    {
        $desk = func::getDesk();
        system::$site_title = "人物管理";

        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['visual_edit'] == true) {
            $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? $_GET['id'] : 1024;
            $role = editorModel::getChar($id, false);
            // system::debug($role);
            if ($role) {
                $pocketItems = '';
                $newArr = array();
                $pItems = "";
                if (count($role->pocket->items) > 0) {
                    foreach ($role->pocket->items as $key => $item) {
                        $item->data = bin2hex(substr($item->data, 0, strlen($item->data)));
                        $newArr[] = $item;
                        $elItem = editorModel::getItemFromElement($item->id);
                        if (isset($desk[$item->id]))
                            $itemDesk = func::reColorDesc($desk[$item->id]);
                        else
                            $itemDesk = "";
                        $pocketItems .= "<img style='padding: 3px' onclick='editPocketItem($key)' data-key='$key' data-target=\"#pocketItem\"  data-tip=\"tooltip\"  data-toggle=\"modal\" title='<b>" . $elItem['name'] . "</b><br>" . $itemDesk . "' src='" . config::$site_adr . "/index.php?function=icon&name={$elItem['icon']}' />";
                    }
                    $pItems = htmlspecialchars(json_encode($newArr));
                }

            $equipmentItems = '';
            if (count($role->equipment->items) > 0) {
                foreach ($role->equipment->items as $item) {
                    $elItem = editorModel::getItemFromElement($item->id);
                    if (isset($desk[$item->id]))
                        $itemDesk = func::reColorDesc($desk[$item->id]);
                    else
                        $itemDesk = "";
                    $equipmentItems .= "<img style='padding: 3px' data-tip=\"tooltip\" title='<b>" . $elItem['name'] . "</b><br>" . $itemDesk . "' src='" . config::$site_adr . "/index.php?function=icon&name={$elItem['icon']}' />";
                }
            }

            stream::putRead($role->status->skills);
            $skills = new roleSkills();
            $skills->count = stream::readInt32(false);
            for ($i = 0; $i < $skills->count; $i++) {
                $skills->skills[$i] = new roleSkill();
                $skills->skills[$i]->id = stream::readInt32(false);
                $skills->skills[$i]->craft = stream::readInt32(false);
                $skills->skills[$i]->level = stream::readInt32(false);
            }
            $role->status->skills = $skills;

            $statusSkills = '';
            $s = file_get_contents(dir . "/system/data/skills.json");
            $s = json_decode($s, true);

            if ($role->status->skills->count > 0) {
                foreach ($role->status->skills->skills as $item) {
                    if (isset($s[$item->id]['icon']))
                        $image = dir . "/system/data/icons_skills/" . base64_decode($s[$item->id]['icon']) . ".png";
                    else
                        $image = dir . "/system/data/icons_skills/unknown.png";
                    if (file_exists($image)) {
                        $src = 'data: ' . mime_content_type($image) . ';base64,' . base64_encode(file_get_contents($image));
                        $statusSkills .= "<img data-toggle=\"tooltip\" title='' style='padding: 3px' src='$src' />";
                    } else {
                        $statusSkills .= "$image ";
                    }
                }
            }
            stream::putRead(stream::$readData_copy, stream::$p_copy);

            system::load("editor");
            //base
            system::set("{id}", $role->base->id);
            system::set("{name}", $role->base->name);
            system::set("{race}", $role->base->race);
            system::set("{gender}", editorModel::selectProp(lang::$gender, $role->base->gender));
            system::set("{cls}", editorModel::selectProp(lang::$cls, $role->base->cls));
            system::set("{spouse}", $role->base->spouse);

            //status
            system::set("{level}", $role->status->level);
            system::set("{level2}", editorModel::selectProp(lang::$level2, $role->status->level2));
            system::set("{exp}", $role->status->exp);
            system::set("{sp}", $role->status->sp);
            system::set("{hp}", $role->status->hp);
            system::set("{mp}", $role->status->mp);
            system::set("{pariah_time}", $role->status->pariah_time);
            system::set("{reputation}", $role->status->reputation);
            system::set("{storehousepasswd}", $role->status->storehousepasswd);
            system::set("{pp}", $role->status->pp);
            system::set("{worldtag}", $role->status->worldtag);
            system::set("{posx}", $role->status->posx);
            system::set("{posy}", $role->status->posy);
            system::set("{posz}", $role->status->posz);
            system::set("{status-skills}", $statusSkills);

            //status - meridian_data
            system::set("{meridian_data-lvl}", $role->status->meridian_data->lvl);
            system::set("{meridian_data-life_dot}", $role->status->meridian_data->life_dot);
            system::set("{meridian_data-die_dot}", $role->status->meridian_data->die_dot);
            system::set("{meridian_data-free_up}", $role->status->meridian_data->free_up);
            system::set("{meridian_data-paid_up}", $role->status->meridian_data->paid_up);
            system::set("{meridian_data-login_days}", $role->status->meridian_data->login_days);

            //pocket
            system::set("{pocket-capacity}", $role->pocket->capacity);
            system::set("{pocket-money}", $role->pocket->money);
            system::set("{pocket-items}", $pocketItems);
            system::set("{pItems}", $pItems);
            //equipment
            system::set("{equipment-items}", $equipmentItems);
            //storehouse
            system::set("{storehouse-capacity}", $role->storehouse->capacity);
            system::set("{storehouse-money}", $role->storehouse->money);

            //property
            system::set("{vitality}", $role->status->property->vitality);
            system::set("{energy}", $role->status->property->energy);
            system::set("{strength}", $role->status->property->strength);
            system::set("{agility}", $role->status->property->agility);
            system::set("{max_hp}", $role->status->property->max_hp);
            system::set("{max_mp}", $role->status->property->max_mp);
            system::set("{max_ap}", editorModel::selectProp(lang::$max_ap, $role->status->property->max_ap));

            system::set("{pvp_cooldown}", $role->status->var_data->pvp_cooldown);
            system::set("{pvp_flag}", $role->status->var_data->pvp_flag);
            system::set("{dead_flag}", editorModel::selectProp(lang::$yn, $role->status->var_data->dead_flag));
            system::set("{pk_count}", $role->status->var_data->pk_count);
            system::show('content');
            system::clear();
        } else {
            system::info("持久性错误“，”无法检索有关字符的数据，可能不存在或服务器关闭");
        }
    } else system::info("没有访问权限，“您无权访问此功能");
}

static function xml()
{
    system::$site_title = "XML编辑器";
    if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['xml_edit'] == true) {

        $id = (is_numeric($_GET['id'])) ? $_GET['id'] : 1024;
        if ($role = editorModel::getChar($id)) {
            system::load("xml");
            system::set("{id}", $id);
            system::set("{xml}", ArrayToXml::toXML($role, "role"));
            system::show('content');
            system::clear();
        } else
            system::info("持久性错误“，”无法检索有关字符的数据，可能不存在或服务器关闭");
    } else system::info("没有访问权限，“”您无权访问此功能");

}

static function chars()
{
    system::$site_title = "角色管理";
    system::load("chars");
    system::set("{save_gamedbg}", "cd " . config::$serverPath . "/" . config::$server['gamedbd']['dir'] . "; ./" . config::$server['gamedbd']['program'] . " " . config::$server['gamedbd']['config'] . " exportclsconfig");
    system::show('content');
    system::clear();
}

}