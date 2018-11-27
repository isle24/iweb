<?php

namespace system\libs\struct;
if (!defined('IWEB')) { die("Error!"); }
class roleStatus
{
    public $version = 1;
    public $level = 0;
    public $level2 = 0;
    public $exp = 0;
    public $sp = 0;
    public $pp = 0;
    public $hp = 0;
    public $mp = 0;
    public $posx = 0;
    public $posy = 0;
    public $posz = 0;
    public $worldtag = 0;
    public $invader_state = 0;
    public $invader_time = 0;
    public $pariah_time = 0;
    public $reputation = 0;
    public $custom_status = "";
    public $filter_data = "";
    public $charactermode = "";
    public $instancekeylist = "";
    public $dbltime_expire = 0;
    public $dbltime_mode = 0;
    public $dbltime_begin = 0;
    public $dbltime_used = 0;
    public $dbltime_max = 0;
    public $time_used = 0;
    public $dbltime_data = "";
    public $storesize = 0;
    public $petcorral = "";
    public $property = "";
    public $var_data = null;
    public $skills = "";
    public $storehousepasswd = "";
    public $waypointlist = "";
    public $coolingtime = "";
    public $npc_relation = "";
    public $multi_exp_ctrl = "";
    public $storage_task = "";
    public $faction_contrib = "";
    public $force_data = "";
    public $online_award = "";
    public $profit_time_data = "";
    public $country_data = "";
    //public $reserved1 = 0;
    public $reserved2 = 0;
   // public $reserved31 = 0;
   // public $reserved32 = 0;
    public $reserved3 = 0;
   // public $reserved4 = 0;
   // public $reserved5 = 0;
    public $king_data = "";
    public $meridian_data = "";
    public $extraprop = "";
    public $title_data = "";
    //public $reserved43 = 0;
    public $reincarnation_data = "";
    public $realm_data = "";
}