<?php
declare(strict_types=1);

namespace App\Tests\Model\Factory;

use App\Constant\Role;
use App\Entity\User;
use App\Model\Factory\UserFactory;
use App\Model\Request\User\CreateUserRequest;
use App\Model\Request\User\UpdateUserRequest;
use App\Model\Response\User\UserResponse;
use App\Tests\Faker\UserFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[CoversClass(UserFactory::class)]
#[CoversClass(UserResponse::class)]
#[CoversClass(CreateUserRequest::class)]
#[CoversClass(UpdateUserRequest::class)]
class UserFactoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<UserPasswordHasherInterface> */
    private ObjectProphecy $passwordHasher;

    private UserFactory $userFactory;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->prophesize(UserPasswordHasherInterface::class);

        $this->userFactory = new UserFactory(
            $this->passwordHasher->reveal(),
        );
    }

    public function testEntityFromRequest(): void
    {
        $userRequest = new CreateUserRequest('henk@devries.nl', 'password', [Role::ROLE_ADMIN]);

        $this->passwordHasher->hashPassword(
            Argument::that(function (User $user) {
                self::assertSame($user->getEmail(), 'henk@devries.nl');
                self::assertSame($user->getRoles(), [Role::ROLE_ADMIN]);
                return true;
            }),
            'password'
        )->willReturn('hashed-password');

        $user = $this->userFactory->entityFromRequest($userRequest);

        self::assertSame('henk@devries.nl', $user->getEmail());
        self::assertSame('hashed-password', $user->getPassword());
        self::assertSame([Role::ROLE_ADMIN], $user->getRoles());
    }

    public function testUpdateEntityFromRequest(): void
    {
        $userRequest = new UpdateUserRequest('henk@devries.nl', [Role::ROLE_ADMIN], 'new-password');
        $user        = new User('other-henk@devries.nl', [Role::ROLE_USER]);

        $user->setPassword('old-password');

        $this->passwordHasher->hashPassword(
            Argument::that(function (User $user) {
                self::assertSame($user->getEmail(), 'henk@devries.nl');
                self::assertSame($user->getRoles(), [Role::ROLE_ADMIN]);
                return true;
            }),
            'new-password'
        )->willReturn('new-hashed-password');

        $user = $this->userFactory->updateEntityFromRequest($userRequest, $user);

        self::assertSame('henk@devries.nl', $user->getEmail());
        self::assertSame('new-hashed-password', $user->getPassword());
        self::assertSame([Role::ROLE_ADMIN], $user->getRoles());
    }

    public function testUpdateEntityFromRequestWithoutPassword(): void
    {
        $userRequest = new UpdateUserRequest('henk@devries.nl', [Role::ROLE_ADMIN]);
        $user        = new User('other-henk@devries.nl', [Role::ROLE_USER]);

        $user->setPassword('hashed-password');

        $this->passwordHasher->hashPassword(Argument::any())->shouldNotBeCalled();

        $user = $this->userFactory->updateEntityFromRequest($userRequest, $user);

        self::assertSame('henk@devries.nl', $user->getEmail());
        self::assertSame('hashed-password', $user->getPassword());
        self::assertSame([Role::ROLE_ADMIN], $user->getRoles());
    }

    public function testResponseFromEntity(): void
    {
        $user = UserFaker::create();

        /** @var string $expectedResponse */
        $expectedResponse = json_encode(new UserResponse($user->getEmail()));

        /** @var string $actualResponse */
        $actualResponse = json_encode(UserFactory::responseFromEntity($user));


        self::assertJsonStringEqualsJsonString(
            $expectedResponse,
            $actualResponse
        );
    }
}
