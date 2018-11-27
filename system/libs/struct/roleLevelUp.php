<?php

namespace system\libs\struct;
if (!defined('IWEB')) { die("Error!"); }
class roleLevelUp{

    public $hp;
    public $mp;
    public $up_hp;
    public $up_mp;
    public $up_dmg;
    public $up_magic;
    
    function classConfig($class_id){
        switch ($class_id){
            case 0:	// 武侠
                $this->hp = 15;
                $this->mp = 9;
                $this->up_hp = 30;
                $this->up_mp = 18;
                $this->up_dmg = 1;
                $this->up_magic = 0;
                break;
            case 1:	// 法师
                $this->hp = 10;
                $this->mp = 14;
                $this->up_hp = 20;
                $this->up_mp = 28;
                $this->up_dmg = 0.2;
                $this->up_magic = 1;
                break;
            case 2:	// 妖精
                $this->hp = 10;
                $this->mp = 14;
                $this->up_hp = 20;
                $this->up_mp = 28;
                $this->up_dmg = 0.2;
                $this->up_magic = 1;
                break;
            case 3:	// 妖兽
                $this->hp = 12;
                $this->mp = 12;
                $this->up_hp = 24;
                $this->up_mp = 24;
                $this->up_dmg = 0.6;
                $this->up_magic = 0.6;
                break;
            case 4:	// 羽灵
                $this->hp = 17;
                $this->mp = 7;
                $this->up_hp = 34;
                $this->up_mp = 14;
                $this->up_dmg = 1;
                $this->up_magic = 0;
                break;
            case 5:	// 羽芒
                $this->hp = 13;
                $this->mp = 10;
                $this->up_hp = 26;
                $this->up_mp = 22;
                $this->up_dmg = 1;
                $this->up_magic = 0;
                break;
            case 6:	// 刺客
                $this->hp = 13;
                $this->mp = 11;
                $this->up_hp = 26;
                $this->up_mp = 22;
                $this->up_dmg = 1;
                $this->up_magic = 0;
                break;
            case 7:	// 巫师
                $this->hp = 10;
                $this->mp = 14;
                $this->up_hp = 20;
                $this->up_mp = 28;
                $this->up_dmg = 0.2;
                $this->up_magic = 1;
                break;
            case 8:	// 剑灵
                $this->hp = 15;
                $this->mp = 9;
                $this->up_hp = 30;
                $this->up_mp = 18;
                $this->up_dmg = 1;
                $this->up_magic = 0;
                break;
            case 9:	// 魅灵
                $this->hp = 10;
                $this->mp = 14;
                $this->up_hp = 20;
                $this->up_mp = 28;
                $this->up_dmg = 0.2;
                $this->up_magic = 1;
                break;
            case 10: // 月仙
                $this->hp = 13;
                $this->mp = 11;
                $this->up_hp = 26;
                $this->up_mp = 22;
                $this->up_dmg = 1;
                $this->up_magic = 0;
                break;
            case 11: // 夜影
                $this->hp = 10;
                $this->mp = 14;
                $this->up_hp = 20;
                $this->up_mp = 28;
                $this->up_dmg = 0.2;
                $this->up_magic = 1;
                break;
        }
        return $this;
    }


    function levelProrepty($cls, $level, &$role)
    {
        $clsConf = $this->classConfig($cls);
        $role->max_hp = ($level - 1) * $clsConf->up_hp + $role->vitality * $clsConf->hp;
        $role->max_mp = ($level - 1) * $clsConf->up_mp + $role->energy * $clsConf->mp;
        $role->damage_low = 1 + floor(($level - 1) * $clsConf->up_dmg);
        $role->damage_high = $role->damage_low;
        $role->damage_magic_low = 1 + floor(($level - 1) * $clsConf->up_magic);
        $role->damage_magic_high = $role->damage_magic_low;
    }

}