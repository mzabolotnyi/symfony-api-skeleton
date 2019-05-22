<?php

namespace App\EventListener\Serializer;

use App\Service\Media\MediaSerializer;
use App\SonataMedia\Media;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;

class SerializeMediaListener
{
    /** @var MediaSerializer */
    private $mediaSerializer;

    public function __construct(MediaSerializer $mediaSerializer)
    {
        $this->mediaSerializer = $mediaSerializer;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /** @var JsonSerializationVisitor $visitor */
        $visitor = $event->getVisitor();

        /** @var Media $media */
        $media = $event->getObject();

        foreach ($this->mediaSerializer->serialize($media) as $field => $value) {
            $visitor->setData($field, $value);
        }
    }
}