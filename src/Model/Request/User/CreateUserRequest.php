<?php
declare(strict_types=1);

namespace App\Model\Request\User;

use App\Constant\Role;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(required: ['email', 'password', 'roles'])]
readonly class CreateUserRequest
{
    #[OA\Property(property: 'email', type: 'string', example: 'henk@devries.nl')]
    #[Assert\NotBlank(message: 'Please submit an email.')]
    #[Assert\Email(message: 'Please submit a valid email.')]
    public string $email;

    #[OA\Property(property: 'password', type: 'string', example: 'ThisIsAPassword!')]
    #[Assert\NotBlank(message: 'Please submit a password.')]
    #[Assert\Length(min: 10, minMessage: 'Please make sure the password is at least 10 characters')]
    public string $password;

    /**
     * @var string[]
     */
    #[OA\Property(
        property: 'roles',
        type: 'array',
        items: new OA\Items(type: 'string', enum: Role::ROLES, example: 'ROLE_USER')
    )]
    #[Assert\Choice(choices: Role::ROLES, multiple: true, message: 'Please submit valid roles.')]
    public array $roles;

    /**
     * @param string[] $roles
     */
    public function __construct(string $email, string $password, array $roles)
    {
        $this->email    = $email;
        $this->password = $password;
        $this->roles    = $roles;
    }
}
