<?php

if (!defined('IWEB')) { die("Error!"); }

include dir . "/system/data/lang/ru/site.php";

function __autoload($name){
    $path = dir . DIRECTORY_SEPARATOR . str_replace("\\", "/", $name) . ".php";

    if (file_exists($path))
        include $path;
    //else
       // \system\libs\system::debug("Error include file: $name");
}