<?php

namespace App\SonataMedia;

use App\Entity\Extra\UuidableEntity;
use Ramsey\Uuid\Uuid;
use Sonata\MediaBundle\Entity\BaseMedia;

/**
 * This file has been generated by the SonataEasyExtendsBundle.
 *
 * @link https://sonata-project.org/easy-extends
 *
 * References:
 * @link http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 */
class Media extends BaseMedia
{
    use UuidableEntity;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $originName;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash($hash): Media
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginName(): ?string
    {
        return $this->originName;
    }

    /**
     * @param string $originName
     * @return Media
     */
    public function setOriginName(string $originName): Media
    {
        $this->originName = $originName;

        return $this;
    }
}
