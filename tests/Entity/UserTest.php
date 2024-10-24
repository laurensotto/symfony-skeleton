<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Constant\Role;
use App\Entity\User;
use App\Exception\InvalidRoleForNewUserException;
use App\Exception\InvalidRoleForUserException;
use App\Tests\Faker\UserFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(User::class)]
#[CoversClass(InvalidRoleForNewUserException::class)]
#[CoversClass(InvalidRoleForUserException::class)]
class UserTest extends TestCase
{
    public function testConstruct(): void
    {
        $user = new User(
            'henk@devries.nl',
            [Role::ROLE_USER]
        );

        $user->setPassword('henkiscool123!');

        self::assertInstanceOf(Uuid::class, $user->getUuid());
        self::assertSame('henk@devries.nl', $user->getEmail());
        self::assertSame('henk@devries.nl', $user->getUserIdentifier());
        self::assertSame('henkiscool123!', $user->getPassword());
        self::assertSame([Role::ROLE_USER], $user->getRoles());

        $user
            ->setEmail('not-henk@devries.nl')
            ->setPassword('henkisnotcool123!')
            ->setRoles([Role::ROLE_ADMIN]);

        self::assertSame('not-henk@devries.nl', $user->getEmail());
        self::assertSame('not-henk@devries.nl', $user->getUserIdentifier());
        self::assertSame('henkisnotcool123!', $user->getPassword());
        self::assertSame([Role::ROLE_ADMIN], $user->getRoles());

        $user
            ->setRoles([Role::ROLE_USER, Role::ROLE_ADMIN]);

        self::assertSame([Role::ROLE_USER, Role::ROLE_ADMIN], $user->getRoles());
    }

    public function testEraseCredentials(): void
    {
        $user = new User(
            'henk@devries.nl',
            [Role::ROLE_USER]
        );

        $user->setPassword('henkiscool123!');

        $user->eraseCredentials();

        self::assertSame('henk@devries.nl', $user->getEmail());
        self::assertSame('henk@devries.nl', $user->getUserIdentifier());
        self::assertSame('henkiscool123!', $user->getPassword());
        self::assertSame([Role::ROLE_USER], $user->getRoles());
    }

    public function testConstructWithInvalidRoleShouldThrow(): void
    {
        $this->expectException(InvalidRoleForNewUserException::class);
        $this->expectExceptionMessage(
            'Invalid role "ROLE_THAT_DOES_NOT_EXIST" provided for new user with email "henk@devries.nl". ' .
            'Allowed values are: ROLE_USER, ROLE_ADMIN'
        );

        new User(
            'henk@devries.nl',
            [Role::ROLE_ADMIN, 'ROLE_THAT_DOES_NOT_EXIST', Role::ROLE_USER]
        );
    }

    public function testSetRolesWithInvalidRoleShouldThrow(): void
    {
        $user = UserFaker::create();

        $this->expectException(InvalidRoleForUserException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid role "ROLE_THAT_DOES_NOT_EXIST" provided for user with UUID "%s". ' .
                'Allowed values are: ROLE_USER, ROLE_ADMIN',
            $user->getUuid()->toRfc4122()
        ));

        $user->setRoles([Role::ROLE_USER, 'ROLE_THAT_DOES_NOT_EXIST']);
    }
}
