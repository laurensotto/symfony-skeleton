<?php
declare(strict_types=1);

namespace App\Tests\Constant;

use App\Constant\Role;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Role::class)]
class RoleTest extends TestCase
{
    public function testIsValidRole(): void
    {
        foreach (Role::ROLES as $role) {
            self::assertTrue(Role::isValidRole($role));
        }
    }
}
