<?php

namespace Sfp\Code\Reflection\BetterReflection;

use Exception;
use Roave\BetterReflection\Reflector\Reflector;
use Roave\BetterReflection\Reflection\ReflectionType as RoaveReflectionType;
use phpDocumentor\Reflection\Type;
use Sfp\Code\Reflection\ReflectionTypeInterface;
use Sfp\Code\Reflection\TypePropertyInterface;

final class TypeProperty implements TypePropertyInterface
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

    /**
     * @throws Exception
     */
    public function getType() : ?ReflectionTypeInterface
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