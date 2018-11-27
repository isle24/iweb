<?php

namespace system\libs\struct;

if (!defined('IWEB')) {die("Error!");}

class roleFactionInfo{

    public $opcode = 0;
    public $status = 0;
    public $unc1 = 0;
    public $unc2 = 0;

    public $fid = 0;
    public $name = "";
    public $level = 0;
    public $master_id = 0;
    public $master_role = 0;
    public $count = 0;
    public $members = null;
    public $announce = "";
    public $sysinfo = "";
}