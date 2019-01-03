<?php
namespace Sfp\Code\Reflection;

interface ReflectionTypeInterface
{
    public function allowsNull () : bool;
    public function isBuiltin () : bool ;
    public function __toString () : string;

    public function targetReflectionClass() : \ReflectionClass;
}