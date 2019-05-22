<?php

namespace App\Service\Media;

use App\SonataMedia\Media;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MediaSerializer
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function serialize(Media $media)
    {
        $result = [
            'id' => $media->getId(),
            'href' => $this->getMediaHref($media),
            'originName' => $media->getOriginName() ?? $media->getName()
        ];

        $mediaFormat = $this->getMediaFormatsWithHref($media);

        if (!empty($mediaFormat)) {
            $result['formats'] = $mediaFormat;
        }

        return $result;
    }

    public function getMediaHref(Media $media, $format = MediaProviderInterface::FORMAT_REFERENCE)
    {
        $provider = $this->container->get($media->getProviderName());
        $reference = $provider->generatePublicUrl($media, $format);
        $href = getenv('API_HOST') . $reference;

        return $href;
    }

    private function getMediaFormatsWithHref(Media $media)
    {
        $provider = $this->container->get($media->getProviderName());
        $mediaContext = $media->getContext();

        $formats = [];

        foreach ($provider->getFormats() as $providerFormat => $settings) {
            if (0 === strpos($providerFormat, $mediaContext)) {
                $key = substr(str_replace($mediaContext, '', $providerFormat), 1);
                $href = $this->getMediaHref($media, $providerFormat);
                $formats[$key] = $href;
            }
        }

        return $formats;
    }
}