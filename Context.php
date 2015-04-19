<?php

class Context {

    public static function get($key=null, $default=null) {
        return self::sgArrValue($_GET, $key, $default);
    }

    public static function post($key=null, $default=null) {
        return self::sgArrValue($_POST, $key, $default);
    }

    public static function server($key=null, $default=null) {
        return self::sgArrValue($_SERVER, $key, $default);
    }

    public static function cookie($key=null, $default=null) {
        return self::sgArrValue($_COOKIE, $key, $default);
    }

    public static function session($key=null, $default=null) {
        if (!isset($_SESSION)) {
            session_start();
        }
        return self::sgArrValue($_SESSION, $key, $default);
    }

    public static function flashCookie($key, $default=null) {
        $cookie = self::cookie($key, $default);
        self::delCookie($key);
        return $cookie;
    }

    public static function flashSession($key, $default=null) {
        $session = self::session($key, $default);
        self::delFromArr($_SESSION, $key);
        return $session;
    }

    public static function setCookie($key, $value, $time=0) {
        if ($value === null) {
            return setcookie($key, '', time() - 3600);
        }
        if ($time == 0) {
            return setcookie($key, $value);
        }
        return setcookie($key, $value, time()+$time);
    }

    public static function setSession($key, $value) {

    }

    public static function delCookie($key) {

    }

    public static function delSession($key) {

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

        switch (count($key)) {
        case 0:
            return $default;

        case 1:
            if (!isset($arr[$key])) {
                return $default;
            }
            return $arr[$key];

        case 2:
            list($k0, $k1) = $key;
            if (!isset($arr[$k0][$k1])) {
                return $default;
            }
            return $arr[$k0][$k1];

        case 3:
            list($k0, $k1, $k2) = $key;
            if (!isset($arr[$k0][$k1][$k2])) {
                return $default;
            }
            return $arr[$k0][$k1][$k2];

        case 4:
            list($k0, $k1, $k2, $k3) = $key;
            if (!isset($arr[$k0][$k1][$k2][$k3])) {
                return $default;
            }
            return $arr[$k0][$k1][$k2][$k3];

        case 5:
            list($k0, $k1, $k2, $k3, $k4) = $key;
            if (!isset($arr[$k0][$k1][$k2][$k3][$k4])) {
                return $default;
            }
            return $arr[$k0][$k1][$k2][$k3][$k4];

        default:
            return $default;
        }
    }

    protected static function delFromArr(&$arr, $key) {
        if (!is_array($key)) {
            return unset($arr[$key]);
        }

        switch (count($key)) {
        case 0:
            return;

        case 1:
            return unset($arr[$key]);

        case 2:
            list($k0, $k1) = $key;
            return unset($arr[$k0][$k1]);

        case 3:
            list($k0, $k1, $k2) = $key;
            return unset($arr[$k0][$k1][$k2]);

        case 4:
            list($k0, $k1, $k2, $k3) = $key;
            return unset($arr[$k0][$k1][$k2][$k3]);

        case 5:
            list($k0, $k1, $k2, $k3, $k4) = $key;
            return unset($arr[$k0][$k1][$k2][$k3][$k4]);
        }
    }

}
