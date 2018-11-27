<?php
session_start();

define("dir", dirname(__FILE__));
define('IWEB', true);

require_once dir . "/system/init.php";

\system\libs\system::run();



