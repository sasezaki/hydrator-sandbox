<?php
namespace Sfp\Mapping\Rules;

interface ClassMetadataManagerInterface
{
    public function getClassMetadata(string $class) : ClassMetadataInterface;
}