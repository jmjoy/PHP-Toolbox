<?php

require_once(dirname(dirname(__DIR__)) . '/Autoloader.php');

Autoloader::add('One', __DIR__ . '/One');
Autoloader::register();

$user1 = new \One\User();
$user2 = new \One\Two\User();

var_dump($user1->say());
var_dump($user2->say());
