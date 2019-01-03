<?php

namespace Sfp\Mapping\BetterReflection {

    use Sfp\Mapping\ClassMetadataInterface;
    use Sfp\Reflection\ReflectionTypeInterface;
    use Sfp\Reflection\TypePropertyInterface;

    use Roave\BetterReflection\Reflector\Reflector;
    use \Roave\BetterReflection\Reflection\ReflectionClass;
    use \Roave\BetterReflection\Reflection\ReflectionType as RoaveReflectionType;

    use \phpDocumentor\Reflection\Type;

    final class ReflectionType implements ReflectionTypeInterface
    {
        private $baseReflectionType;

        public function __construct(RoaveReflectionType $reflectionType)
        {
            $this->baseReflectionType = $reflectionType;
        }


    }

    class TypeProperty implements TypePropertyInterface
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

    class ClassMetadata implements ClassMetadataInterface
    {
        private $reflectionClass;

        public function __construct(ReflectionClass $reflectionClass)
        {
            $this->reflectionClass = $reflectionClass;
        }

        public function getProperty(string $name): TypePropertyInterface
        {
            $internalProperty = $this->reflectionClass->getProperty($name);
            $types = $internalProperty->getDocBlockTypes();
//            /** @var \phpDocumentor\Reflection\Type $type */
//            foreach ($types as $type) {
//                if ($type instanceof \phpDocumentor\Reflection\Types\Object_) {
//                //    $className = $type->getFqsen()->getName();
//                    // $baseHydrator->addStrategy($key, $this->getStrategy($className));
//                }
//            }

            return new TypeProperty($type, false);
        }
    }
}


namespace {

    use Zend\Hydrator\ReflectionHydrator;

    use Sfp\Reflection\ {
        TypedProperty
    };

    use Sfp\Mapping\ {
        ClassMetadataInterface, ClassMetadataManagerInterface
    };

    class Hydrator
    {
        /** @var ReflectionHydrator  */
        private $baseHydrator;
        /** @var ClassMetadataManagerInterface */
        private $classMetadataManager;

        public function __construct(ReflectionHydrator $baseHydrator, $classMetadataManager)
        {
            $this->baseHydrator = $baseHydrator;
            $this->classMetadataManager = $classMetadataManager;
        }

        public function hydrate(array $data, object $object)
        {
            $baseHydrator = clone $this->baseHydrator;
            $classMetadata = $this->classMetadataManager->getClassMetadata(get_class($object));
            foreach ($data as $key => $value) {
                $property = $classMetadata->getProperty($key);
                $types = $property->getDocBlockTypes();
                /** @var \phpDocumentor\Reflection\Type $type */
                foreach ($types as $type) {
                    if ($type instanceof \phpDocumentor\Reflection\Types\Object_) {
                        $className = $type->getFqsen()->getName();
                        $baseHydrator->addStrategy($key, $this->getStrategy($className));
                    }
                }
            }

            return $baseHydrator->hydrate($data, $object);
        }

        private function getStrategy(string $className) : \Zend\Hydrator\Strategy\StrategyInterface
        {
            if ($className === DateTimeImmutable::class) {
                return new \Zend\Hydrator\Strategy\DateTimeFormatterStrategy('Y/m/d H:i:s');
            }

            var_dump($className);
        }
    }

    require __DIR__ . '/vendor/autoload.php';


    class Entity
    {
        /**
         * @var \DateTimeImmutable
         */
        private $start;
    }


    $classInfo = (new \Roave\BetterReflection\BetterReflection())
        ->classReflector()
        ->reflect(Entity::class);

    $classMetadataManager = new class implements ClassMetadataManagerInterface
    {
        public function getClassMetadata(string $class) : ClassMetadataInterface
        {
            $reflectionClass = (new \Roave\BetterReflection\BetterReflection())
                ->classReflector()
                ->reflect($class);

            return new class($reflectionClass) implements ClassMetadataInterface {
                /** @var \Roave\BetterReflection\Reflection\Adapter\ReflectionClass  */
                private $classInfo;

                public function __construct(\Roave\BetterReflection\Reflection\ReflectionClass $classInfo)
                {
                    $this->classInfo = $classInfo;
                }

                public function getProperty(string $name): \Roave\BetterReflection\Reflection\ReflectionProperty
                {
                    return $this->classInfo->getProperty($name);
                }
            };
        }
    };

    $data = [
        'start' => '2018/11/01 10:00:00'
    ];

    /** @var \Zend\Hydrator\ReflectionHydrator $hydrator */
    $hydrator = (new \Zend\Hydrator\StandaloneHydratorPluginManager())
        ->get(\Zend\Hydrator\ReflectionHydrator::class);

    $entity = new Entity();

    $hydrator = new Hydrator($hydrator, $classMetadataManager);
    $hydrator->hydrate($data, $entity);

    var_dump($entity);
}