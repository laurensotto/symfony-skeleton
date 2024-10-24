<?php
declare(strict_types=1);

namespace App\Exception;

use App\Constant\Role;

class InvalidRoleForNewUserException extends \InvalidArgumentException
{
    public function __construct(string $email, string $role)
    {
        parent::__construct(
            sprintf(
                'Invalid role "%s" provided for new user with email "%s". Allowed values are: %s',
                $role,
                $email,
                implode(', ', Role::ROLES)
            ),
        );
    }
}
