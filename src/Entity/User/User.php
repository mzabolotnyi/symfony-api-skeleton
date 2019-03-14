<?php

namespace App\Entity\User;

use App\Entity\Extra\BlameableEntity;
use App\Entity\Extra\TimestampableEntity;
use App\Entity\Extra\UuidableEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AttributeOverride;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\Column;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;

/**
 * @AttributeOverrides({
 *      @AttributeOverride(name="username",
 *          column=@Column(
 *              nullable=true,
 *              unique=false,
 *              type="string",
 *              name="username"
 *          )
 *      ),@AttributeOverride(name="usernameCanonical",
 *          column=@Column(
 *              nullable=true,
 *              unique=false,
 *              type="string",
 *              name="username_canonical"
 *          )
 *      )
 * })
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 * @ORM\Table(name="user")
 * @Gedmo\Loggable
 */
class User extends BaseUser
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

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     * @Gedmo\Versioned
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", nullable=true)
     * @Gedmo\Versioned
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", nullable=true)
     * @Gedmo\Versioned
     */
    private $middleName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $registeredAt;

    /**
     * @Gedmo\Versioned
     */
    protected $email;

    public function __construct()
    {
        parent::__construct();
        $this->uuid = Uuid::uuid4();
        $this->enabled = true;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeInterface
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(?\DateTimeInterface $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }
}
