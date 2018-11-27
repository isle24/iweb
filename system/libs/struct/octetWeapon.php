<?php

namespace system\libs\struct;

if (!defined('IWEB')) {
    die("Error!");
}

class octetWeapon{

    public $level = 0;
    public $class = 0;
    public $StrReq = 0;
    public $ConReq = 0;
    public $DexReq = 0;
    public $IntReq = 0;
    public $CurDurab = 0;
    public $MaxDurab = 0;
    public $ItemClass = 0;
    public $ItemFlag = 0;
    public $Creator = "";
    public $NeedAmmo = 0;
    public $WeaponClass = 0;
    public $Rang = 0;
    public $AmmoType = 0;
    public $MinPhysAtk = 0;
    public $MaxPhysAtk = 0;
    public $MinMagAtk = 0;
    public $MaxMagAtk = 0;
    public $AtkSpeed = 0;
    public $Distance = 0;
    public $FragDistance = 0;
    public $SlotInfo = NULL;
    public $BonusInfo = NULL;

}