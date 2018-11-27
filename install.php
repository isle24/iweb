<?php
session_start();
define('dir', dirname(__FILE__));
header("Content-Type: text/html; charset=utf-8");
$url = explode("/install.php", strtolower($_SERVER['PHP_SELF']));
$url = reset($url);
if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) $protocol = "https://"; else $protocol = "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $url;

if (isset($_POST['save'])) {
    $confWrite = "<?php\nnamespace system\data;\nif (!defined('IWEB')) {die(\"Error!\");}\nclass config\n{\n";
    $confWrite.= '    static $users = array(
        //Ваш пользователь со всеми правами
        //Для изменения доступа у нужной функции замените true на false
        array("username" => "'.$_POST['config']['username'].'", "password" => "'.$_POST['config']['password'].'", "permission" =>             
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
    );'."\n";
    foreach ($_POST['config'] as $key => $value) {
        if ($key != "username" && $key != "password") {
            if ($value == "true" or $value == "false")
                $confWrite .= "\tstatic $$key = $value;\n";
            else
                $confWrite .= "\tstatic $$key = \"$value\";\n";
        }
    }

    $confWrite .= "\t" . 'static $server = array(
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
            "dir" => "auth/build/",
            "program" => "authd.sh",
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

}';
    $fw = fopen(dir . "/system/data/config.php", "w");
    if (fwrite($fw, $confWrite)) {
        $_SESSION['user'] = $_POST['config']['username'];
        echo "设置已成功记录，现在您可以开始使用面板了！ 
		本后台由汉化有OMK小K汉化<br>
               <b>一定要删除install.php文件<br>
                 <b>OMG小K汉化</b>
<br><center><a href='$url'>转到面板的主页面</a></center>";
    } else {
        echo "Ошибка записи в файл!";
    }
    fclose($fw);
} else {
    ?>

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>完美后台管理 </title>
        <link rel="stylesheet" href="<?php echo $url; ?>/system/template/css/bootstrap.min.css">
        <style>
            * {
                margin: 0;
                padding: 0;
            }

            body {
                background: #1a2023;
                color: #aeb9bc;
                font-family: Tahoma, Arial, Helvetica, sans-serif;
            }

            .container input[type=text] {
                padding-bottom: 5px;
                background: #353e41;
                border: 1px solid #161a1c;
                color: white;
            }

            .container input[type=text]:focus {
                background: #343d40;
                border: 1px solid #161a1c;
                color: white;
            }

            .container select {
                padding-bottom: 5px;
                background: #353e41;
                border: 1px solid #161a1c;
                color: white;
            }

            .container select:focus {
                background: #343d40;
                border: 1px solid #161a1c;
                color: white;
            }

            .content {
                background: #262e31;
            }
        </style>
    </head>
    <body>

    <div class="container content">
        <?php
        if (is_writable(dir . "/system/data")) { ?>
        <H3>完美后台管理</H3>
        <form method="post" action="">
            <h5>Iweb访问</h5>
            用户名：: <input class="form-control form-control-sm" type="text" name="config[username]">
            <small class="form-text text-muted">面板访问的用户名</small>
            密码： <input class="form-control form-control-sm" type="text" name="config[password]">
            <small class="form-text text-muted">访问面板的密码</small>

            IP访问：: <select class="form-control form-control-sm" type="text" name="config[access]">
                <option value="true">启用</option>
                <option value="false" selected>关闭</option>
            </select>
            <small class="form-text text-muted">您可以启用IP访问来充分保护面板</small>

            IP列表：: <input class="form-control form-control-sm" type="text" name="config[accessIP]">
            <small class="form-text text-muted">您可以指定一个范围，例如：192.168.0.0/24和通常的IP：192.168.0.12。 多个IP点通过;
                
            </small>

            <hr>
            <h5>系统的参数</h5>

            网站地址： <input class="form-control form-control-sm" type="text" name="config[site_adr]"
                                value="<?php echo $url; ?>">
            <small class="form-text text-muted">地址是自动确定的</small>

            标题： <input class="form-control form-control-sm" type="text" name="config[site_title]"
                             value="IWEB">

            聊天部件： <select class="form-control form-control-sm" name="config[widgetChat]" id="">
                <option value="on">显示</option>
                <option value="off">关闭</option>
            </select>
            <small class="form-text text-muted">在面板的所有页面上都会显示一个包含游戏聊天的窗口</small>

            操作日志： <select class="form-control form-control-sm" name="config[logActions]" id="">
                <option value="true">启用</option>
                <option value="false">关闭</option>
            </select>
            <small class="form-text text-muted">在控制面板中记录用户活动</small>

            <hr>
            <h5>数据库</h5>
            地址： <input class="form-control form-control-sm" type="text" name="config[db_host]" value="服务器地址">
            用户名: <input class="form-control form-control-sm" type="text" name="config[db_user]"
                                     value="root">
            密码: <input class="form-control form-control-sm" type="text" name="config[db_password]">
            库名: <input class="form-control form-control-sm" type="text" name="config[db_table]" value="wm">
            编码: <input class="form-control form-control-sm" type="text" name="config[db_charset]" value="utf8">

            <hr>
            <h5>游戏邮件</h5>
            发件人: <input class="form-control form-control-sm" type="text" name="config[titleMail]"
                                    value=" GM">
            文本信件: <input class="form-control form-control-sm" type="text" name="config[messageMail]"
                                value="消息文本">

            <hr>
            <h5>服务器设置</h5>
            版本: <select class="form-control form-control-sm" name="config[version]">
                <option value="151">1.5.1</option>
                <option value="153">1.5.3</option>
            </select>
            Gamedbd端口： <input class="form-control form-control-sm" type="text" name="config[dbPort]" value="29400">
            GDeliveryd端口: <input class="form-control form-control-sm" type="text" name="config[gdeliverydPort]"
                                    value="29100">
            GProvider端口: <input class="form-control form-control-sm" type="text" name="config[GProviderPort]"
                                   value="29300">
            glink端口: <input class="form-control form-control-sm" type="text" name="config[linkPort]" value="29000">
            <small class="form-text text-muted">检查主页面上北方状态的端口</small>

            IP服务器: <input class="form-control form-control-sm" type="text" name="config[serverIP]"
                              value="127.0.0.1">
            <small class="form-text text-muted">如果IWEB将远程工作并且您的端口已打开，请更改。不安全！                
            </small>

            服务器文件夹: <input class="form-control form-control-sm" type="text" name="config[serverPath]"
                                  value="/home">

            身份验证过程的名称是： <select class="form-control form-control-sm" type="text" name="config[serverTypeAuth]">
                <option value="auth">auth</option>
                <option value="authd">authd</option>
                <option value="gauthd">gauthd</option>
            </select>
            <small class="form-text text-muted">指定用于定义状态的过程的名称（启用/禁用）            </small>
            elements.data的路径： <input class="form-control form-control-sm" type="text" name="config[elementPath]"
                                  value="/home/gamed/config">
            聊天服务器文件: <input class="form-control form-control-sm" type="text" name="config[chatFile]"
                                  value="/home/logs/world2.chat">
            <hr>
            <h5>访问服务器</h5>
            <div class="alert alert-info">
                有可能不指定，这不影响脚本的操作！服务器的运行/停止功能不可用            </div>
            <small class="form-text text-muted">要工作，你还需要安装屏幕（apt-get install screen） 和一个php libssh2库，你还没有安装它                screen)<br>
                和一个php libssh2库，你还没有安装它
                 <?php if (extension_loaded('ssh2')) echo "<span style='color: green'>установлена</span>"; else echo "<span style='color: red'>не установлена</span>"; ?>
            </small>
            SSH主机： <input class="form-control form-control-sm" type="text" name="config[ssh_host]" value="本地服务器">
            SSH用户： <input class="form-control form-control-sm" type="text" name="config[ssh_user]"
                                     value="root">
            SSH密码: <input class="form-control form-control-sm" type="text" name="config[ssh_pass]">
            SSH端口: <input class="form-control form-control-sm" type="text" name="config[ssh_port]" value="22">
            <br>
            <center><input class="btn btn-success col-sm-4" type="submit" name="save" value="确定设置">
            </center>
            <br>
            <?php } else { ?>
                <div style="padding: 20px">
                    <div class="alert alert-warning" role="alert">
                        <b>Доступ к записи запрещен</b>
                        <p>Установите права <b>777</b> для записи на папку <?php echo dir . "/system/data" ?></p>
                    </div>
                </div>
            <?php } ?>
        </form>
    </div>
    <script href="<?php echo $url; ?>/system/template/js/bootstrap.min.js"></script>

    </body>
    </html>
    <?php
} ?>