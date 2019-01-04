<?php

use Sfp\Code\Reflection\Interfaces\ReflectionPropertyInterface;
use Zend\Hydrator\ReflectionHydrator;

use Sfp\Mapping\Rules\ {
    ClassMetadataInterface, ClassMetadataManagerInterface
};

class Hydrator
{
    /** @var ReflectionHydrator  */
    private $baseHydrator;
    /** @var ClassMetadataManagerInterface */
    private $classMetadataManager;

    public function __construct(ReflectionHydrator $baseHydrator, ClassMetadataManagerInterface $classMetadataManager)
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

            public function __construct(Roave\BetterReflection\Reflection\ReflectionClass $classInfo)
            {
                $this->classInfo = $classInfo;
            }

            public function getProperty(string $name): ReflectionPropertyInterface
            {
                $property = $this->classInfo->getProperty($name);
                var_dump();
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
