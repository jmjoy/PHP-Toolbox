<?php

/**
 *  类自动加载器
 *  (仅支持命名空间)
 *  @author JM_Joy
 */
final class Autoloader {

    /**
     * 通用配置
     * @var array
     */
    private static $config = array(
        'suffix'    =>  '.php',
    );

    /**
     * Autoload规则数组
     * @var array
     */
    private static $rules = array();

    /**
     * Disable construct
     */
    private function __construct() {
    }

    /**
     * 添加一条autoload规则
     * @param string $name 命名空间第一级名称
     * @param string $dir 文件夹路径
     * @param array $config 配置，目前仅有suffix
     */
    public static function add($name, $dir, $config=array()) {
        static::$rules[$name] = array(
            'dir'       =>  $dir,
            'config'   =>  array_merge(static::$config, $config),
        );
    }

    /**
     * 删除一条autoload规则
     * @param string $name 命名空间第一级名称
     */
    public static function delete($name) {
        unset(static::$rules[$name]);
    }

    /**
     * autoload函数，不应该手动调用
     * @param string $class 类名
     */
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

    /**
     * 注册autoload
     */
    public static function register() {
        spl_autoload_register(array('Autoloader', 'autoload'));
    }

}
