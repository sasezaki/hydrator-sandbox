<?php
namespace Sfp\Mapping\Rules;

use Sfp\Code\Reflection\Interfaces\ReflectionPropertyInterface;

interface ClassMetadataInterface
{
    public function getProperty(string $name) : ReflectionPropertyInterface;
}
