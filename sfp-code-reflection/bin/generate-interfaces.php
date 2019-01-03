#!/usr/bin/php
<?php
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodGenerator;
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/functions.php';

$ref = new ReflectionExtension('Reflection');
$proto = analyse_proto(__DIR__ . '/../php_reflection.c');

$parentMethods = get_parent_methods($ref);

const NAMESPACE_NAME = 'Sfp\\Code\\Reflection\\Interfaces';


/** @var ReflectionClass $class */
foreach ($ref->getClasses() as $class) {

    $interfaceGenerator = new InterfaceGenerator;
    $interfaceGenerator->setNamespaceName(NAMESPACE_NAME);
    $interfaceGenerator->setName($class->getName() . 'Interface');

    if (isset($parentMethods[$class->getName()])) {
        $parentInterface = sprintf('%s\\%sInterface', NAMESPACE_NAME, $parentMethods[$class->getName()]['__PARENT_CLASS__']);
        $interfaceGenerator->setImplementedInterfaces([$parentInterface]);
    }

    foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if (isset($parentMethods[$class->getName()]) && in_array($method->getName(), $parentMethods[$class->getName()])) {
            continue;
        }

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

function get_parent_methods(ReflectionExtension $ref) {

    $parentMethods = [];
    foreach ($ref->getClasses() as $class) {
        /** @var ReflectionClass $parent */
        $parent = $class->getParentClass();
        if ($parent === false) {
            continue;
        }
        $parentMethods[$class->getName()]['__PARENT_CLASS__'] = $parent->getName();

        foreach ($parent->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $parentMethods[$class->getName()][] = $method->getName();
        }
    }
    return $parentMethods;
}