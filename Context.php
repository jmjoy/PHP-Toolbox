<?php

class Context {

    public static function get($key=null, $default=null) {
        return sgArrValue($_GET, $key, $default);
    }

    public static function post($key=null, $default=null) {
        return sgArrValue($_POST, $key, $default);
    }

    public static function server($key=null, $default=null) {
        return sgArrValue($_SERVER, $key, $default);
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
