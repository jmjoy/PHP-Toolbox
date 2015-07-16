<?php

/**
 * PHP路由器
 */
final class Router {

    /**
     * 配置
     */
    public static $_config = array(
        'dir'                 =>  array(),
        'url_ext'             =>  array(),
        'filename_suffix'     =>  'Controller',
        'filename_ext'        =>  '.php',
        'create_ext'          =>  '.html',
        'default_controller'  =>  'Index',
        'default_action'      =>  'index',
    );

    /**
     * 配置方法
     */
    public static function config($config) {
        static::$_config = array_merge(static::$_config, $config);
    }

    /**
     * 直接跑吧
     */
    public static function run($pathinfo) {
        if ($pathinfo === null) {
            $pathinfo = '';
        }
        $slice = implode('/', $pathinfo);

        $last = $slice[count($slice) - 1];
        $url_ext = '';
        // TODO 没有写下去的冲动！
        if (strpos($last, '.') === 0) {
        }

        //switch (count($slice)) {
        //case 0:
        //case 1:

        //}
        
    }

    /**
     * 直接生成
     */
    public static function create($url, $params, $ext='') {
    }

}
