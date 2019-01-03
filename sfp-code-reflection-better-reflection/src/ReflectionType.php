<?php
namespace Sfp\Code\Reflection\BetterReflection;

use Roave\BetterReflection\Reflection\ReflectionType as RoaveReflectionType;
use Sfp\Code\Reflection\ReflectionTypeInterface;

final class ReflectionType implements ReflectionTypeInterface
{
    private $baseReflectionType;

    public function __construct(RoaveReflectionType $reflectionType)
    {
        $this->baseReflectionType = $reflectionType;
    }

    public function allowsNull () : bool
    {
        return $this->baseReflectionType->allowsNull();
    }
    public function isBuiltin () : bool
    {
        return $this->baseReflectionType->isBuiltin();
    }

    public function __toString () : string
    {
        return $this->baseReflectionType->__toString();
    }

    public function targetReflectionClass() : \ReflectionClass
    {
        return $this->baseReflectionType->targetReflectionClass();
    }
}