<?php
declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\Constant\Role;
use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[CoversClass(UserFixtures::class)]
class UserFixturesTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<UserPasswordHasherInterface> $passwordHasher */
    private ObjectProphecy $passwordHasher;

    private UserFixtures $fixtures;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->prophesize(UserPasswordHasherInterface::class);

        $this->fixtures = new UserFixtures($this->passwordHasher->reveal());
    }

    public function testLoad(): void
    {
        $manager = $this->prophesize(ObjectManager::class);

        $this->passwordHasher->hashPassword(Argument::any(), 'test')
            ->shouldBeCalledTimes(2)
            ->willReturn('test-hashed');

        $manager->flush()->shouldBeCalledTimes(1);

        $manager->persist(Argument::that(function (User $user) {
            if ($user->getEmail() === 'user@test.nl') {
                self::assertSame([Role::ROLE_USER], $user->getRoles());
                self::assertSame('test-hashed', $user->getPassword());
            }

            if ($user->getEmail() === 'admin@test.nl') {
                self::assertSame([Role::ROLE_ADMIN], $user->getRoles());
                self::assertSame('test-hashed', $user->getPassword());
            }

            return true;
        }))->shouldBeCalledTimes(2);

        $this->fixtures->load($manager->reveal());
    }
}
