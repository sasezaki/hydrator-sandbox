<?php
require_once  __DIR__ .'/functions.php';

//var_dump(analyse_proto(__DIR__ . '/../php_reflection.c'));
$methods = analyse_proto(__DIR__ . '/../php_reflection.c');

var_dump($methods['ReflectionClass']);