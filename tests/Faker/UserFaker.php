<?php
declare(strict_types=1);

namespace App\Tests\Faker;

use App\Constant\Role;
use App\Entity\User;

class UserFaker
{
    /** Default option, returns a random user. Use this unless a specific configuration is required. */
    const int RANDOM = 0;

    /** Returns a user */
    const int USER = 1;

    /** Returns a user with admin permissions */
    const int ADMIN = 2;

    private const array OPTIONS = [
        self::USER,
        self::ADMIN,
    ];

    public static function create(int $option = self::RANDOM): User
    {
        return match ($option) {
            self::USER => self::createUser(Role::ROLE_USER),
            self::ADMIN => self::createUser(Role::ROLE_ADMIN),
            default => self::create(self::OPTIONS[array_rand(self::OPTIONS)])
        };
    }

    private static function createUser(string $role): User
    {
        $user = new User(
            'henk@devries.nl',
            [$role]
        );

        return $user->setPassword('henkiscool123!');
    }
}
