<?php
declare(strict_types=1);

namespace App\Exception;

use App\Constant\Role;
use App\Entity\User;

class InvalidRoleForUserException extends \InvalidArgumentException
{
    public function __construct(User $user, string $role)
    {
        parent::__construct(
            sprintf(
                'Invalid role "%s" provided for user with UUID "%s". Allowed values are: %s',
                $role,
                $user->getUuid(),
                implode(', ', Role::ROLES)
            ),
        );
    }
}
