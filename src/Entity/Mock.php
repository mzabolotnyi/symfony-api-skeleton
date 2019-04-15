<?php

namespace App\Entity;

use App\Entity\Extra\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use App\Constant\Serialization\Group;

/**
 * @ORM\Table(name="mock")
 * @ORM\Entity(repositoryClass="App\Repository\MockRepository")
 */
class Mock extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups(Group::LIST)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups(Group::LIST)
     */
    private $description;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
