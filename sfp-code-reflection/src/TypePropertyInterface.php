<?php
namespace Sfp\Code\Reflection;

interface TypePropertyInterface
{
    // represents ReflectionProperty method for Typed Property since PHP 7.4
    public function getType() : ?ReflectionTypeInterface;
    public function hasType() : bool;
}