<?php
namespace Sfp\Mapping\Rules;

//use Sfp\Code\Reflection\;

interface ClassMetadataInterface
{
    public function getProperty(string $name) : ReflectionProperyInterface;
}
