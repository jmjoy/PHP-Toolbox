<?php

/**
 *  类自动加载器
 *  @author JM_Joy
 */
final class Autoloader {

    private static $config = array(
        'suffix'    =>  '.php',
    );

    private static $rules = array();

    private function __construct() {
    }

    public static function add($name, $dir, $config=array()) {
        static::$rules[$name] = array(
            'dir'       =>  $dir,
            'config'   =>  array_merge(static::$config, $config),
        );
    }

    public static function delete($name) {
        unset(static::$rules[$name]);
    }

    public static function autoload($class) {
        $arr = explode('\\', $class);
        if (count($arr) <= 0 || !isset(static::$rules[$arr[0]])) {
            throw new Exception('没有注册autoload规则：' . $arr[0]);
        }

        $rule = static::$rules[$arr[0]];
        $path = $rule['dir'] . DIRECTORY_SEPARATOR;
        for ($i = 1; $i < count($arr) - 1; $i++) {
            $path .= $arr[$i] . DIRECTORY_SEPARATOR;
        }

        $path .= $arr[count($arr) - 1] . $rule['config']['suffix'];
        require($path);
    }

    public static function register() {
        spl_autoload_register(array('Autoloader', 'autoload'));
    }

}
