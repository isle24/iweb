<?php

namespace system\libs;

use system\data\config;
use system\libs\element\element;
use system\libs\struct\GMRoleData;
use system\models\editorModel;
use system\models\indexModel;
use system\models\serverModel;

if (!defined('IWEB')) {
    die("Error!");
}

class system
{

    private static $template, $data;
    public static $site_title, $version = "1.1", $result = array("content" => "", "adr" => "");

    static function debug($value)
    {
        echo "<pre style='background: #bdbdbd; border: 1px solid #5b6669; border-radius: 2px; padding: 10px'>";
        if (is_array($value) or is_object($value)) {
            print_r($value);
        } else {
            echo $value;
        }
        echo "</pre>";
    }

    static function accept()
    {
        $accept = explode(";", config::$accessIP);
        if (count($accept) > 0 && config::$access == true) {
            foreach ($accept as $value) {
                if (stristr($value, "/")) {
                    $diapason = explode("/", $value);
                    $realIP = explode(".", $_SERVER['REMOTE_ADDR']);
                    $acceptIP = explode(".", $diapason[0]);
                    if ($realIP[0] == $acceptIP[0] && $realIP[1] == $acceptIP[1] && $realIP[2] >= $acceptIP[2] && $realIP[2] <= $diapason[1] && $realIP[3] >= $acceptIP[3] && $realIP[3] <= $diapason[1])
                        return true;
                } else {
                    if ($_SERVER['REMOTE_ADDR'] == $value)
                        return true;
                }
            }
        } else return true;
        return false;
    }

    static function jms($type, $message)
    {
        echo json_encode(array("type" => $type, "message" => $message));
    }

    static function run()
    {
        header("Content-Type: text/html; charset=utf-8");
        if (file_exists(dir . "/system/data/config.php")) {
            if (self::accept()) {
                if (isset($_GET['function']))
                    self::goJS($_GET['function']);
                else {
                    self::routing();

                    self::load('widgetChat');
                    self::show('widgetChat');
                    self::clear();

                    self::load("main");
                    self::set("{title}", config::$site_title);
                    self::set("{content}", self::$result['content']);
                    if (config::$widgetChat == "on")
                        self::set("{widgetChatPattern}", self::$result['widgetChat']);
                    else
                        self::set("{widgetChatPattern}", "");
                    self::set("{widgetChat}", config::$widgetChat);
                    self::show("main", true);
                    self::clear(true);
                }
            } else {
                die("No access");
            }
        } else {
            header("Location: install.php");
        }
    }

    static function log($action){
        if (config::$logActions === true){
            if (!empty($action)){
                database::query("INSERT INTO panel_logs (ip, date, user, action) VALUES ('".$_SERVER['REMOTE_ADDR']."','".time()."','".$_SESSION['user']."','".$action."')");
            }
        }
    }

    static function goJS($function)
    {
        if ((boolean)preg_match("#^[aA-zZ0-9\-_]+$#", $function)) {
            if (isset($_SESSION['id'])) $idS = $_SESSION['id']; else $idS = 0;
            if (isset($_SESSION['user']) == config::$users[$idS]['username']) {
                switch ($_GET['function']) {
                    case "sendxml":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['xml_edit'] == true) {
                            editorModel::putChar($_POST['id'], ArrayToXml::fromXML($_POST['xml']));
                            self::log("改变了字符。“$ _ POST ['id']。” 通过XML");
                        } else system::jms("info", "您无权访问此功能");
                        break;

                    case "sendmail":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['send_mail'] == true) {
                            serverModel::mail($_POST);
                        } else system::jms("info“，”你没有访问这个功能");

                        break;

                    case "sendvisual":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['visual_edit'] == true) {
                            editorModel::saveVisual($_POST);
                        } else system::jms("info", "您无权访问此功能");
                        break;

                    case "teleport":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['teleport'] == true) {
                            editorModel::teleportGD($_POST['id']);
                        } else system::jms("info", "您无权访问此功能");

                        break;

                    case "nullchar":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['null_exp_sp'] == true) {
                            editorModel::nullSpEp($_POST['id']);
                        } else system::jms("info", "您无权访问此功能");

                        break;

                    case "levelup":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['level_up'] == true) {
                            editorModel::levelUp($_POST['id'], $_POST['level']);
                        } else system::jms("info", "您无权访问此功能");

                        break;

                    case "sendmsg":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['send_msg'] == true) {
                            serverModel::sendChatMessage($_POST['msg'], $_POST['chanel']);
                        } else system::jms("info", "您无权访问此功能");

                        break;

                    case "startServer":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['server_manager'] == true) {
                            serverModel::startServer();
                        } else system::jms("info", "您无权访问此功能");
                        break;

                    case "restartServer":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['server_manager'] == true) {
                            serverModel::restartServer();
                        } else system::jms("info", "您无权访问此功能");

                        break;

                    case "stopServer":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['server_manager'] == true) {
                            serverModel::stopServer();
                        } else system::jms("info", "您无权访问此功能");
                        break;

                    case "kickrole":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['kick_role'] == true) {

                            GMRoleData::kickUser($_POST['id'], 1, "GM Edit Account");
                        } else system::jms("info", "您无权访问此功能");

                        break;

                    case "addcash":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['add_gold'] == true) {

                            editorModel::addGold($_POST['id'], $_POST['gold']);
                        } else system::jms("info", "您无权访问此功能");

                        break;

                    case "getrole":
                        editorModel::charsList($_POST['id']);
                        break;

                    case "delrole":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['del_role'] == true) {
                            editorModel::deleteRole($_POST['id']);
                        } else system::jms("info", "您无权访问此功能");
                        break;

                    case "renamerole":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['rename_role'] == true) {
                            editorModel::renameRole($_POST['id'], $_POST['oldname'], $_POST['newname']);
                        } else system::jms("info", "您无权访问此功能");

                        break;

                    case "checkGM":
                        indexModel::gm($_POST['id'], "check");
                        break;

                    case "managerGM":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['gm_manager'] == true) {

                            indexModel::gm($_POST);
                        } else system::jms("info", "您无权访问此功能");

                        break;

                    case "getmailitems":
                        echo serverModel::getItemsMail($_POST['id']);
                        break;

                    case "sendmailall":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['send_mail'] == true) {
                            serverModel::sendMailAllOnline($_POST);
                        } else system::jms("info", "您无权访问此功能");
                        break;

                    case "icon":
                        if (isset($_GET['name'])) $name = $_GET['name']; else $name = 'unknown.dds';

                        if (database::query("SELECT * FROM iconItems WHERE name='{$name}'")) {
                            if (database::num() > 0) {
                                $icon = database::assoc();
                            }else{
                                $re = database::query("SELECT * FROM iconItems WHERE name='unknown.dds'");
                                $icon = database::assoc($re);
                            }
                            Header("Content-type: image/jpeg");
                            echo $icon['icon'];
                        }
                        break;

                    case "goreadelement":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['settings'] == true) {
                            element::read();
                        } else system::jms("info", "У вас нет доступа к данной функции");
                        break;

                    case "gouploadicon":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['settings'] == true) {
                            func::uploadImage("iconlist_ivtrm");
                        } else system::jms("info", "您无权访问此功能");
                        break;

                    case "killpid":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['settings'] == true) {
                            exec("kill " . $_POST['pid']);
                        } else system::jms("info", "您无权访问此功能");
                        break;

                    case "ban":
                        if (config::$users[$_SESSION['id']]['username'] == $_SESSION['user'] && config::$users[$_SESSION['id']]['permission']['ban'] == true) {
                            editorModel::ban($_POST['id'],$_POST['time'],$_POST['type'],$_POST['reason']);
                        } else system::jms("info", "您无权访问此功能");
                        break;

                    case "getchat":
                        $chat = func::readChat();
                        if (isset($chat[$_GET['chl']])) {
                            $tag = "";
                            $color = "white";
                            switch ($_GET['chl']) {
                                case 0:
                                    $tag = "<span class='badge badge-secondary'>全部</span>";
                                    $color = "white";
                                    break;

                                case 1:
                                    $tag = "<span class='badge badge-warning'>世界</span>";
                                    $color = "yellow";
                                    break;

                                case 2:
                                    $tag = "<span class='badge badge-success'>队伍</span>";
                                    $color = "lightgreen";
                                    break;

                                case 3:
                                    $tag = "<span class='badge badge-primary'>帮派</span>";
                                    $color = "lightblue";

                                    break;
                            }
                            $chat[$_GET['chl']] = array_reverse($chat[$_GET['chl']], true);
                            foreach ($chat[$_GET['chl']] as $msg) {
                                if ($msg['clan'] != 0) $clan = "->" . $msg['clan']; else $clan = "";
                                echo $tag . " <span class='charName'>" . $msg['role'] . $clan . "</span>: <span style='color: $color'>" . $msg['msg'] . "</span><br>";
                            }
                        } else echo "没有消息!";
                        break;
                }
            } else {
                if ($function == "auth") {
                    indexModel::login($_POST['username'], $_POST['password']);
                }
            }
        } else self::notFound();
    }

    static function routing()
    {
        $controller = (isset($_GET["controller"]) && (boolean)preg_match("#^[aA-zZ0-9\-_]+$#", $_GET["controller"]) == true) ? $_GET["controller"] : "";
        $page = (isset($_GET["page"]) && (boolean)preg_match("#^[aA-zZ0-9\-_]+$#", $_GET["page"]) == true) ? $_GET["page"] : "";
        if (isset($_SESSION['id'])) $idS = $_SESSION['id']; else $idS = 0;
        if (!isset($_SESSION['user']) == config::$users[$idS]['username']) {
            call_user_func("system\\controllers\\indexContrl::login");
        } else {
            if ($controller == "")
                if (is_callable("system\\controllers\\indexContrl::index"))
                    call_user_func("system\\controllers\\indexContrl::index");
                else
                    system::notFound();
            else {
                $action = (empty($page)) ? "index" : $page;
                if (is_callable("system\\controllers\\{$controller}Contrl::" . $action))
                    call_user_func("system\\controllers\\{$controller}Contrl::" . $action);
                else
                    system::notFound();
            }
        }

    }

    static function info($title, $message, $type = 'primary')
    {
        self::load('info');
        self::set("{title}", $title);
        self::set("{message}", $message);
        self::set("{type}", $type);
        self::show("content");
        self::clear();

    }

    static function notFound()
    {
        $page404 = file_get_contents(dir . "/system/template/404.html");
        header("HTTP/1.0 404 Not Found");
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        echo $page404;
        die();
    }

//Templater
    static function load($name)
    {

        $path = dir . DIRECTORY_SEPARATOR . "system" . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR . $name . ".html";

        if (file_exists($path)) {
            self::$template = file_get_contents($path);
            self::$template = str_replace("{adr}", config::$site_adr, self::$template);
            self::$template = str_replace("{site_title}", self::$site_title, self::$template);
        } else
            self::debug("Template not found: $path");

    }

    static function set($name, $value)
    {
        self::$data[$name] = $value;
    }

    static function show($name, $show = false)
    {
        if (self::$data) {
            foreach (self::$data as $key => $value) {
                self::$template = str_replace($key, $value, self::$template);
            }
        }

        self::$result[$name] = self::$template;

        if ($show)
            echo self::$result[$name];

    }

    static function clear($full = false)
    {
        self::$template = "";
        self::$data = array();
        if ($full) {
            self::$result = array("content" => "", "adr" => "");
        }
    }

}