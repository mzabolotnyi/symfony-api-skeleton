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
use JMS\Serializer\Annotation as Serializer;
use App\Constant\Serialization\Group;

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
     * @Serializer\Groups(Group::LIST)
     * @ORM\Column(name="last_name", type="string", nullable=true)
     * @Gedmo\Versioned
     */
    private $lastName;

    /**
     * @var string
     *
     * @Serializer\Groups(Group::LIST)
     * @ORM\Column(name="first_name", type="string", nullable=true)
     * @Gedmo\Versioned
     */
    private $firstName;

    /**
     * @var string
     *
     * @Serializer\Groups(Group::LIST)
     * @ORM\Column(name="middle_name", type="string", nullable=true)
     * @Gedmo\Versioned
     */
    private $middleName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="email_confirmed_at", type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $emailConfirmedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="need_email_confirm", type="boolean", nullable=true)
     * @Gedmo\Versioned
     */
    private $needEmailConfirm;

    /**
     * @Serializer\Groups(Group::LIST)
     * @Gedmo\Versioned
     */
    protected $email;

    /**
     * @Gedmo\Versioned
     */
    protected $enabled;

    /**
     * @Gedmo\Versioned
     */
    protected $confirmationToken;

    /**
     * @Gedmo\Versioned
     */
    protected $passwordRequestedAt;

    /**
     * @Serializer\Groups(Group::DETAIL)
     * @Gedmo\Versioned
     */
    protected $roles;

    public function __construct()
    {
        parent::__construct();
        $this->uuid = Uuid::uuid4()->toString();
        $this->enabled = true;
        $this->needEmailConfirm = false;
    }

    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
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

    public function getEmailConfirmedAt(): ?\DateTimeInterface
    {
        return $this->emailConfirmedAt;
    }

    public function setEmailConfirmedAt(?\DateTimeInterface $emailConfirmedAt): self
    {
        $this->emailConfirmedAt = $emailConfirmedAt;

        return $this;
    }

    public function isEmailConfirmed(): bool
    {
        return null !== $this->emailConfirmedAt;
    }

    public function setNeedEmailConfirm(bool $needEmailConfirm): User
    {
        $this->needEmailConfirm = $needEmailConfirm;

        return $this;
    }

    public function needConfirmEmail(): bool
    {
        return $this->needEmailConfirm && !$this->isEmailConfirmed();
    }
}
