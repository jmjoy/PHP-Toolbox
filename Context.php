<?php

/**
 * 单个Http对话的上下文类
 * @author JM_Joy
 */
class Context {

    /**
     * 获取get
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @param mixed $default 如果要获取的值不存在就返回这个默认值，默认是null
     * @return mixed
     */
    public static function get($key=null, $default=null) {
        return self::getFromArr($_GET, $key, $default);
    }

    /**
     * 获取post
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @param mixed $default 如果要获取的值不存在就返回这个默认值，默认是null
     * @return mixed
     */
    public static function post($key=null, $default=null) {
        return self::getFromArr($_POST, $key, $default);
    }

    /**
     * 获取server
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @param mixed $default 如果要获取的值不存在就返回这个默认值，默认是null
     * @return mixed
     */
    public static function server($key=null, $default=null) {
        return self::getFromArr($_SERVER, $key, $default);
    }

    /**
     * 获取cookie
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @param mixed $default 如果要获取的值不存在就返回这个默认值，默认是null
     * @return mixed
     */
    public static function cookie($key=null, $default=null) {
        return self::getFromArr($_COOKIE, $key, $default);
    }

    /**
     * 获取session
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @param mixed $default 如果要获取的值不存在就返回这个默认值，默认是null
     * @return mixed
     */
    public static function session($key=null, $default=null) {
        if (!isset($_SESSION)) {
            session_start();
        }
        return self::getFromArr($_SESSION, $key, $default);
    }

    /**
     * 设置Cookie
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @param mixed $value 要设置的值
     * @param bool|int $time cookie保存的秒数，如果为false表示浏览器进程的Cookie
     */
    public static function setCookie($key, $value, $time=false) {
        $key = self::cookieKey($key);

        // 键为空或者空数组，不要理会
        if ($key === '') {
            return;
        }

        // 表示浏览器进程的cookie
        if ($time === false) {
            return setcookie($key, $value);
        }

        return setcookie($key, $value, time()+$time);
    }

    /**
     * 设置Session
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
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


    /**
     * 获取闪存cookie
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @param mixed $default 如果要获取的值不存在就返回这个默认值，默认是null
     * @return mixed
     */
    public static function flashCookie($key, $default=null) {
        $cookie = self::cookie($key, $default);
        self::delCookie($key);
        return $cookie;
    }

    /**
     * 获取闪存session
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @param mixed $default 如果要获取的值不存在就返回这个默认值，默认是null
     * @return mixed
     */
    public static function flashSession($key, $default=null) {
        $session = self::session($key, $default);
        self::delSession($key);
        return $session;
    }

    /**
     * 删除cookie
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     */
    public static function delCookie($key) {
        self::setCookie($key, '', -3600);
    }

    /**
     * 删除session
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     */
    public static function delSession($key) {
        if (!isset($_SESSION)) {
            session_start();
        }

        self::delFromArr($_SESSION, $key);
    }

    /**
     * 从数组中获取值
     * @param array $arr 超全局变量
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @param mixed $default 如果要获取的值不存在就返回这个默认值，默认是null
     * @return mixed
     */
    protected static function getFromArr(&$arr, $key, $default) {
        // key为null，获取整个数组
        if ($key === null) {
            return $arr;
        }

        // key为单值，作为数组的键
        if (!is_array($key)) {
            if (!isset($arr[$key])) {
                return $default;
            }
            return $arr[$key];
        }

        // key是空数组，返回默认值
        if (!count($key)) {
            return $default;
        }

        // key是数组，层次地去取值
        foreach ($key as $subKey) {
            if (!isset($arr[$subKey])) {
                return $default;
            }
            $arr = &$arr[$subKey];
        }

        return $arr;
    }

    /**
     * 从数组中删除值
     * @param array $arr 超全局变量
     * @param mixed $key 如果是单值，直接作为键；如果是数组，则是层次性的键
     * @return mixed
     */
    protected static function delFromArr(&$arr, $key) {
        // key为单值，删除数组对应的键的值
        if (!is_array($key)) {
            unset($arr[$key]);
            return;
        }

        // key为空数组，不执行动作
        $count = count($key);
        if (!$count) {
            return;
        }

        // key是数组且有一个元素，以这个元素为键删除值
        if ($count == 1) {
            unset($arr[$key[0]]);
        }

        // key是数组且有多个元素，层次找到键并删除值
        for ($i = 0; $i < $count - 1; $i++) {
            if (!isset($arr[$key[$i]])) {
                return;
            }
            $arr = &$arr[$key[$i]];
        }

        unset($arr[$key[$count-1]]);
    }

    /**
     * 通过单值或数组组装cookie的键
     * @param mixed 可以是单值，也可以是数组
     * @return string 返回字符串的键值
     *
     */
    protected function cookieKey($key) {
        if (!is_array($key)) {
            return $key;
        }

        $count = count($key);
        if (!$count) {
            return '';
        }

        if ($count == 1) {
            return $key[0];
        }

        $tmpKey = $key[0];
        for ($i = 1; $i < $count; $i++) {
            $tmpKey .= "[{$key[$i]}]";
        }

        return $tmpKey;
    }

}
