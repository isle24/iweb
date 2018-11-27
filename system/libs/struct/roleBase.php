<?php

namespace system\libs\struct;
if (!defined('IWEB')) { die("Error!"); }
class roleBase{

    public $version = 1;
    public $id = 0;
    public $name = "";
    public $race = 0;
    public $cls = 0;
    public $gender = 0;
    public $custom_data = "";
    public $config_data = "";
    public $custom_stamp = 0;
    public $status = 0;
    public $delete_time = 0;
    public $create_time = 0;
    public $lastlogin_time = 0;
    public $forbid = NULL;
    public $help_states = "";
    public $spouse = 0;
    public $userid = 0;
   // public $reserved2 = 0;  ver < 80
    public $cross_data = "";
    public $reserved2_ = 0;
    public $reserved3 = 0;
    public $reserved4 = 0;

}