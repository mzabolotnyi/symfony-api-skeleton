<?php

namespace App\Aware;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerAware
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * @required
     *
     * @param EntityManagerInterface $em
     */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEm(): EntityManagerInterface
    {
        return $this->em;
    }
}