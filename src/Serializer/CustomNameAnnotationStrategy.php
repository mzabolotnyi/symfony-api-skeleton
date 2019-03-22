<?php

namespace App\Serializer;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

class CustomNameAnnotationStrategy implements PropertyNamingStrategyInterface
{
    public function translateName(PropertyMetadata $property): string
    {
        if (null !== $name = $property->serializedName) {
            return $name;
        }

        return $property->name;
    }
}
