<?php

require_once(dirname(dirname(__DIR__)) . '/Autoloader.php');

Autoloader::add('One', __DIR__ . '/One');
Autoloader::add('Ten', __DIR__ . '/Three');
Autoloader::register();

$user1 = new \One\User();
$user2 = new \One\Two\User();
$user3 = new \Ten\Four\User();

var_dump($user1->say());
var_dump($user2->say());
var_dump($user3->say());
