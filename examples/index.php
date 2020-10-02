<?php

session_start();

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Class/User.php';

$data = [
    'param' => '<a href="#">Param</a>',
    'obj' => new User()
];

//require "DefaultUseExample.php";
require "MiddlewareExample.php";
