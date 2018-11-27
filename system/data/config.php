<?php
namespace system\data;
if (!defined('IWEB')) {die("Error!");}
ini_set('display_errors', false);
class config
{
    static $users = array(
        //Ваш пользователь со всеми правами
        //Для изменения доступа у нужной функции замените true на false
        array("username" => "isle", "password" => "isle0204", "permission" =>             
            array(
                "xml_edit" => true, //Страница: ХМЛ редактор
                "visual_edit" => true, //Страница: Визуальный редактор
                "gm_manager" => true, //Страница: Главная. ГМ права
                "kick_role" => true, //Страница: Главная. Кикнуть персонажа
                "ban" => true, //Страница: Главная. блокировка персонажа\аккаунта\чата
                "add_gold" => true, //Страница: Персонажи,главная. Выдача голда на акк
                "level_up" => true, //Страница: Персонажи. Изменение уромня
                "rename_role" => true, //Страница: Персонажи. Переименование персонажа
                "teleport" => true, //Страница: Персонажи. Телепорт в ГД
                "null_exp_sp" => true, //Страница: Персонажи. Обнуление духа и опыта
                "del_role" => true, //Страница: Персонажи. Удаление персонажа
                "server_manager" => true, //Страница: Менеджер сервера. Остановка,запуск,рестат сервера
                "send_msg" => true, //Страница: Менеджер сервера отправка сообщения в чат
                "send_mail" => true, //Страница отправки почты
                "settings" => true, //Страница настроек
                "logs" => true //Страница Логов
            )
        ),
    );
	static $access = false;
	static $accessIP = "";
	static $site_adr = "http://admin.2iwm.com/iweb";
	static $site_title = "IWEB";
	static $widgetChat = "on";
	static $logActions = true;
	static $db_host = "172.27.64.7";
	static $db_user = "wm";
	static $db_password = "412a9a2bd127";//412a9a2bd127
	static $db_table = "wm";
	static $db_charset = "utf8";
	static $titleMail = " GM";
	static $messageMail = "消息文本";
	static $version = "153";
	static $dbPort = "29400";
	static $gdeliverydPort = "29100";
	static $GProviderPort = "29300";
	static $linkPort = "29000";
	static $serverIP = "127.0.0.1";
	static $serverPath = "/root/pwserver";
	static $serverTypeAuth = "authd";
	static $elementPath = "/root/pwserver/gamed/config";
	static $chatFile = "/root/pwserver/logs/world2.chat";
	static $ssh_host = "localhost";
	static $ssh_user = "root";
	static $ssh_pass = "Kyl199424";
	static $ssh_port = "22";
	static $server = array(
        "logservice" => array(
            "dir" => "logservice",
            "program" => "logservice",
            "config" => "logservice.conf",
            "screen" => true
        ),
        "uniquenamed" => array(
            "dir" => "uniquenamed",
            "program" => "uniquenamed",
            "config" => "gamesys.conf",
            "screen" => true
        ),
        "auth" => array(
            "dir" => "authd/build/",
            "program" => "authd",
            "config" => "start",
            "pid_name" => array("auth" => "auth", "authd" => "authd", "gauthd" => "gauthd"),
            "screen" => false,
        ),
        "gamedbd" => array(
            "dir" => "gamedbd",
            "program" => "gamedbd",
            "config" => "gamesys.conf",
            "screen" => true
        ),
        "gacd" => array(
            "dir" => "gacd",
            "program" => "gacd",
            "config" => "gamesys.conf",
            "screen" => true
        ),
        "gfactiond" => array(
            "dir" => "gfactiond",
            "program" => "gfactiond",
            "config" => "gamesys.conf",
            "screen" => true
        ),
        "gdeliveryd" => array(
            "dir" => "gdeliveryd",
            "program" => "gdeliveryd",
            "config" => "gamesys.conf",
            "screen" => true
        ),
        "glinkd" => array(
            "dir" => "glinkd",
            "program" => "glinkd",
            "config" => "gamesys.conf 1",
            "screen" => true
        ),
        "gs" => array(
            "dir" => "gamed",
            "program" => "gs",
            "config" => "gs01",
            "screen" => true
        )
    );

    static $serverStop = array(
        "glinkd",
        "logservices",
        "java",
        "gacd",
        "gs",
        "gfactiond",
        "gdeliveryd",
        "uniquenamed",
        "gamedbd",
        );

}
