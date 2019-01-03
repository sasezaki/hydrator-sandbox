<?php

namespace Sfp\Code\Reflection\BetterReflection;

use Roave\BetterReflection\Reflector\Reflector;
use \phpDocumentor\Reflection\Type;

final class TypeProperty
{
    /** @var Type */
    private $type;

    /** @var bool */
    private $allowsNull;

    /** @var Reflector */
    private $reflector;

    public function __construct(?Type $type, bool $allowsNull)
    {
        $this->type = $type;
        $this->allowsNull = $allowsNull;
    }

    public function getType() : ReflectionTypeInterface
    {
        if (!$this->type instanceof Type) {
            throw new Exception();
        }

        return new ReflectionType(RoaveReflectionType::createFromTypeAndReflector(
            $this->type->__toString(),
            $this->allowsNull,
            $this->reflector
        ));
    }

    public function hasType() : bool
    {
        return $this->type instanceof Type;
    }
}