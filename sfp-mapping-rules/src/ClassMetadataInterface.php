<?php
namespace Sfp\Mapping\Rules;

//use \ReflectionProperty;
//use Roave\BetterReflection\Reflection\ReflectionProperty;
use Sfp\Code\ReflectionInterfaces\ReflectionProperyInterface;

interface ClassMetadataInterface
{
    public function getProperty(string $name) : ReflectionProperyInterface;
}
