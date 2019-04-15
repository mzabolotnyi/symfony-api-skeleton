<?php

namespace App\Entity\Extra;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

abstract class BaseEntity
{
    use TimestampableEntity,
        BlameableEntity,
        UuidableEntity;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function isNew(): bool
    {
        return null === $this->getId();
    }
}