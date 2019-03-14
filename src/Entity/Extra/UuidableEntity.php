<?php

namespace App\Entity\Extra;

use Doctrine\ORM\Mapping as ORM;

trait UuidableEntity
{
    /**
     * @var string
     * @ORM\Column(type="string", length=36, nullable=false, unique=true)
     */
    protected $uuid;

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }
}