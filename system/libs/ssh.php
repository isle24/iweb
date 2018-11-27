<?php

namespace system\libs;
use system\data\config;
if (!defined('IWEB')) { die("Error!"); }
class ssh{
    
    static $conn;
    static $error;
    static $stream;

    static function start() {
        if (extension_loaded('ssh2')) {
            if (self::connect(config::$ssh_host, config::$ssh_port)) {
                if (self::auth_pwd(config::$ssh_user, config::$ssh_pass)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }else {
            self::$error = '未安装php模块libssh2';
            return false;
        }
    }

    static function connect($host,$port=22) {
        if (self::$conn = @ssh2_connect($host, $port)) {
            return true;
        } else {
            self::$error = '[x] Can not connected to '.$host.':'.$port;
            return false;
        }
    }

    static function auth_pwd($u,$p) {
        if (@ssh2_auth_password(self::$conn, $u, $p)) {
            return true;
        } else {
            self::$error = 'Login Failed';
            return false;
        }
    }

    static function send_file($localFile,$remoteFile,$permision) {
        if (@ssh2_scp_send(self::$conn, $localFile, $remoteFile, $permision)) {
            return true;
        } else {
            self::$error = 'Can not transfer file';
            return false;
        }
    }

    static function get_file($remoteFile,$localFile) {
        if (@ssh2_scp_recv(self::$conn, $remoteFile, $localFile)) {
            return true;
        } else {
            return false;
        }
    }

    static function exec($cmd) {
        self::$stream=@ssh2_exec(self::$conn, $cmd);
        @stream_set_blocking( self::$stream, true );
    }

    static function out() {
        $line = '';
        while ($get=fgets(self::$stream)) {
            $line.=$get;
        }
        return $line;
    }

    static function stop() {
        // if disconnect function is available call it..
        if ( function_exists('ssh2_disconnect') ) {
            @ssh2_disconnect(self::$conn);
        } else { // if no disconnect func is available, close conn, unset var
            @fclose(self::$conn);
            //unset(self::$conn);
        }
        // return null always
        return NULL;
    }
}