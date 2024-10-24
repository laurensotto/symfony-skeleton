<?php
declare(strict_types=1);

namespace App\Entity;

use App\Constant\Role;
use App\Exception\InvalidRoleForNewUserException;
use App\Exception\InvalidRoleForUserException;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table('`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $uuid;

    #[ORM\Column(unique: true)]
    private string $email;

    #[ORM\Column]
    private string $password;

    /** @var string[] $roles */
    #[ORM\Column]
    private array $roles;

    /**
     * @param string[] $roles
     */
    public function __construct(string $email, array $roles)
    {
        foreach ($roles as $role) {
            if (!Role::isValidRole($role)) {
                throw new InvalidRoleForNewUserException($email, $role);
            }
        }

        $this->uuid  = Uuid::v4();
        $this->email = $email;
        $this->roles = $roles;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        foreach ($roles as $role) {
            if (!Role::isValidRole($role)) {
                throw new InvalidRoleForUserException($this, $role);
            }
        }

        $this->roles = $roles;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function eraseCredentials(): void
    {
    }
}
