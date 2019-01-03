#!/usr/bin/php
<?php
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/functions.php';

$ref = new ReflectionExtension('Reflection');
$proto = analyse_proto(__DIR__ . '/../php_reflection.c');


/** @var ReflectionClass $class */
foreach ($ref->getClasses() as $class) {

    $interfaceGenerator = new InterfaceGenerator;
    $interfaceGenerator->setNamespaceName('Sfp\\Code\\Reflection\\Interfaces');
    $interfaceGenerator->setName($class->getName() . 'Interface');

    foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

        $methodReflection = new Zend\Code\Reflection\MethodReflection($class->getName(), $method->getName());
        $methodGenerator = MethodGenerator::fromReflection($methodReflection);
        if (NULL !== $methodReflection->getReturnType()) {
            $methodGenerator->setReturnType($methodReflection->getReturnType());
        } else {
            if (isset($proto[$class->getName()][$method->getName()]['return'])) {
                $returnType = $proto[$class->getName()][$method->getName()]['return'];
                if ($returnType !== 'void') {
                    if ($returnType === 'stdclass') {
                        $returnType = '\\stdClass';
                    }
                    $methodGenerator->setReturnType($returnType);
                }
            }
        }

        if (isset($proto[$class->getName()][$method->getName()]['comment'])) {
            $methodGenerator->setDocBlock($proto[$class->getName()][$method->getName()]['comment']);
        }
        $interfaceGenerator->addMethodFromGenerator($methodGenerator);
    }

    echo $interfaceGenerator->generate();
}