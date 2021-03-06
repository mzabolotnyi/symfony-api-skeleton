<?php

namespace App\Repository\OAuth;

use App\Entity\OAuth\RefreshToken;
use App\Repository\BaseRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshToken[]    findAll()
 * @method RefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefreshTokenRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }
}
