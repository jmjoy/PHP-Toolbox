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

    /**
     * 设置Session
     * @param mixed $key 如果是单值，直接作为session的键；如果是数组，则是层次性的
     * @param mixed $value 要设置的值
     */
    public static function setSession($key, $value) {
        // session动态
        if (!isset($_SESSION)) {
            session_start();
        }

        // $key不是数组
        if (!is_array($key)) {
            $_SESSION[$key] = $value;
            return;
        }

        // $key是空数组，不处理
        $count = count($key);
        if (!$count) {
            return;
        }

        // $key数组只有一个值的情况，把值当session的键
        if ($count == 1) {
            $_SESSION[$key[0]] = $value;
            return;
        }

        // $key数组有多个值的情况
        if (!isset($_SESSION[$key[0]])) {
            $session = array();
        } else {
            $session = $_SESSION[$key[0]];
        }
        $tmpArr = &$session;

        for ($i = 1; $i < $count - 1; $i++) {
            if (!isset($tmpArr[$i]) || !is_array($tmpArr[$i])) {
                $tmpArr[$key[$i]] = array();
            }
            $tmpArr = &$tmpArr[$key[$i]];
        }

        $tmpArr[$key[$count - 1]] = $value;
        $_SESSION[$key[0]] = $session;
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
