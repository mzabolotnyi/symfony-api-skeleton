<?php

namespace App\Service\Media;

use App\Exception\RuntimeException;
use App\Entity\Media\Media;
use Sonata\MediaBundle\Filesystem\Local;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sonata\MediaBundle\Entity\MediaManager as SonataMediaManager;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaManager
{
    const CONTEXT_DEFAULT = 'default';
    const CONTEXT_IMAGE = 'image';
    const CONTEXT_VIDEO = 'video';

    const PROVIDER_FILE = 'sonata.media.provider.file';
    const PROVIDER_IMAGE = 'sonata.media.provider.image';

    /** @var ContainerInterface */
    private $container;

    /** @var SonataMediaManager */
    private $sonataMediaManager;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->sonataMediaManager = $container->get('sonata.media.manager.media');
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->sonataMediaManager, $name], $arguments);
    }

    public function createFromUploadedFile(UploadedFile $uploadedFile): Media
    {
        try {
            $tempPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
            $fileContent = file_get_contents($uploadedFile->getPathName());
            file_put_contents($tempPath, $fileContent);

            $hash = md5($fileContent);

            $mimeType = $uploadedFile->getClientMimeType();
            $providerName = $this->detectProviderName($mimeType);
            $context = $this->detectContext($mimeType);

            $media = $this->createMedia($tempPath, $hash, $context, $providerName, $uploadedFile->getClientOriginalName());

        } catch (\Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $media;
    }

    private function detectContext($mimeType)
    {

        if (\in_array($mimeType, $this->getImageMimeTypes())) {
            $context = self::CONTEXT_IMAGE;
        } else {
            $context = self::CONTEXT_DEFAULT;
        }

        return $context;
    }

    private function detectProviderName($mimeType)
    {

        if (\in_array($mimeType, $this->getImageMimeTypes())) {
            $providerName = self::PROVIDER_IMAGE;
        } else {
            $providerName = self::PROVIDER_FILE;
        }

        return $providerName;
    }

    private function getImageMimeTypes(): array
    {
        return [
            'image/jpeg',
            'image/png'
        ];
    }

    private function createMedia($binaryContent, $hash, $context, $providerName, $originName = null): Media
    {
        /** @var Media $media */
        $media = $this->sonataMediaManager->findOneBy(['hash' => $hash]);

        if ($media === null || !$this->fileExists($media)) {

            $media = $media === null ? new Media() : $media;
            $media->setBinaryContent($binaryContent);
            $media->setContext($context);
            $media->setProviderName($providerName);
            $media->setHash($hash);
            $media->setOriginName($originName);

            $this->sonataMediaManager->save($media);
        }

        return $media;
    }

    private function fileExists(Media $media): bool
    {
        /** @var MediaProviderInterface $provider */
        $provider = $this->container->get($media->getProviderName());

        /** @var Local $adapter */
        $adapter = $provider->getFilesystem()->getAdapter();

        $path = $adapter->getDirectory()
            . DIRECTORY_SEPARATOR
            . $provider->generatePrivateUrl($media, MediaProviderInterface::FORMAT_REFERENCE);

        return file_exists($path);
    }
}