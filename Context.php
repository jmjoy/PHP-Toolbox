<?php

class Context {

    public static function get($key=null, $default=null) {
        return $this->sgArrValue($_GET, $key, $default);
    }

    public static function post($key=null, $default=null) {
        return $this->sgArrValue($_POST, $key, $default);
    }

    public static function server($key=null, $default=null) {
        return $this->sgArrValue($_SERVER, $key, $default);
    }

    public static function cookie($key=null, $default=null) {
        return $this->sgArrValue($_COOKIE, $key, $default);
    }

    public static function session($key=null, $default=null) {
        if (!isset($_SESSION)) {
            session_start();
        }
        return $this->sgArrValue($_SESSION, $key, $default);
    }

    public static function flashCookie($key, $default=null) {
        $cookie = $this->cookie($key, $default);
        unset($_COOKIE[$key]);
        return $cookie;
    }

    public static function flashSession($key, $default=null) {
        $session = $this->session($key, $default);
        unset($_SESSION[$key]);
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

    protected static function sgArrValue($sgArr, $key, $default) {
        if ($key === null) {
            return $sgArr;
        }
        if (isset($sgArr[$key])) {
            return $sgArr[$key];
        }
        return $default;
    }

}
