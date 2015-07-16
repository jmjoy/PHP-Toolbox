<?php

require_once('D:/git/PHP-Toolbox/Router.php');

Router::config(array(
    'dir'                 =>  'name',
    'ext'                 =>  '.html',
    'suffix'              =>  'Controller',
    'extension'           =>  '.php',
    'default_controller'  =>  'Index',
    'default_action'      =>  'index',
));
Router::run($_SERVER['REQUEST_URI']);
Router::create('a/b/c', array('a', 'b', 'c'), '.html');
