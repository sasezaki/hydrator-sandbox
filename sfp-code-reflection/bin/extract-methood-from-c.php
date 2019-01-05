<?php
require_once  __DIR__ .'/functions.php';


$methods = analyse_proto(__DIR__ . '/../php_reflection.c');

if (isset($argv[1])) {
    var_dump($methods['ReflectionClass'][$argv[1]]);
} else {
    var_dump($methods['ReflectionClass']);
}