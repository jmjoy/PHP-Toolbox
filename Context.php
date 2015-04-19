<?php

class Context {

    public static function get($key=null, $default=null) {
        return self::getFromArr($_GET, $key, $default);
    }

    public static function post($key=null, $default=null) {
        return self::getFromArr($_POST, $key, $default);
    }

    public static function server($key=null, $default=null) {
        return self::getFromArr($_SERVER, $key, $default);
    }

    public static function cookie($key=null, $default=null) {
        return self::getFromArr($_COOKIE, $key, $default);
    }

    public static function session($key=null, $default=null) {
        if (!isset($_SESSION)) {
            session_start();
        }
        return self::getFromArr($_SESSION, $key, $default);
    }

    public static function setCookie($key, $value, $time=false) {
        if ($time === false) {
            return setcookie($key, $value);
        }
        return setcookie($key, $value, time()+$time);
    }

    public static function setSession($key, $value) {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (!is_array($key)) {
            $_SESSION[$key] = $value;
            return;
        }

        // 空数组
        if (!$count = count($key)) {
            return;
        }

        $tmpArr = &$_SESSION;
        for ($i = 0; $i < $count - 1; $count++) {
            $tmpArr[$key[$i]] = array();
            $tmpArr = $tmpArr[$key[$i]];
        }

        echo str_repeat('finish', 1024);flush();

        $tmpArr[$key[$count-1]] = $value;
    }

    public static function flashCookie($key, $value) {
        $cookie = self::cookie($key, $default);
        self::delCookie($key);
        return $cookie;
    }

    public static function flashSession($key, $value) {
        $session = self::session($key, $default);
        self::delSession($key);
        return $session;
    }

    public static function delCookie($key) {
        setcookie($key, '', time() - 3600);
    }

    public static function delSession($key) {
        if (!isset($_SESSION)) {
            session_start();
        }

        self::delFromArr($_SESSION, $key);
    }

    protected static function getFromArr(&$arr, $key, $default) {
        if ($key === null) {
            return $arr;
        }

        if (!is_array($key)) {
            if (!isset($arr[$key])) {
                return $default;
            }
            return $arr[$key];
        }

        // 空数组
        if (!count($key)) {
            return $default;
        }

        foreach ($key as $subKey) {
            if (!isset($arr[$subKey])) {
                return $default;
            }
            $arr = &$arr[$subKey];
        }

        return $arr;
    }

    protected static function delFromArr(&$arr, $key) {
        if (!is_array($key)) {
            unset($arr[$key]);
            return;
        }

        foreach ($arr as $subKey) {
            if (!isset($arr[$subKey])) {
                return;
            }
            $arr = &$arr[$subKey];
        }

        unset($arr);
    }

}
