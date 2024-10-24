<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Constant\Role;
use App\Controller\V1\UserController;
use App\Entity\User;
use App\Model\Factory\UserFactory;
use App\Model\Request\User\CreateUserRequest;
use App\Model\Request\User\UpdateUserRequest;
use App\Security\UserProvider;
use App\Tests\Faker\UserFaker;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

#[CoversClass(UserController::class)]
class UserControllerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<EntityManagerInterface> $entityManager */
    private ObjectProphecy $entityManager;

    /** @var ObjectProphecy<UserFactory> $userFactory */
    private ObjectProphecy $userFactory;

    /** @var ObjectProphecy<UserProvider> $userProvider */
    private ObjectProphecy $userProvider;

    private UserController $userController;

    protected function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->userFactory   = $this->prophesize(UserFactory::class);
        $this->userProvider  = $this->prophesize(UserProvider::class);

        $this->userController = new UserController(
            $this->entityManager->reveal(),
            $this->userFactory->reveal(),
            $this->userProvider->reveal()
        );
    }

    public function testGet(): void
    {
        $user = UserFaker::create();

        /** @var string $actualResponse */
        $actualResponse = $this->userController->get($user)->getContent();

        /** @var string $expectedResponse */
        $expectedResponse = json_encode(UserFactory::responseFromEntity($user));

        self::assertJsonStringEqualsJsonString(
            $expectedResponse,
            $actualResponse
        );
    }

    public function testMe(): void
    {
        $user = UserFaker::create();

        $this->userProvider->getUser()->willReturn($user);

        /** @var string $actualResponse */
        $actualResponse = $this->userController->me()->getContent();

        /** @var string $expectedResponse */
        $expectedResponse = json_encode(UserFactory::responseFromEntity($user));

        self::assertJsonStringEqualsJsonString(
            $expectedResponse,
            $actualResponse
        );
    }

    public function testPost(): void
    {
        $userRequest = new CreateUserRequest('henk@devries.nl', 'password', [Role::ROLE_ADMIN]);
        $user        = new User('henk@devries.nl', [Role::ROLE_ADMIN]);

        $user->setPassword('hashed-password');

        $this->userFactory->entityFromRequest($userRequest)->willReturn($user);
        $this->entityManager->persist($user)->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        /** @var string $actualResponse */
        $actualResponse = $this->userController->post($userRequest)->getContent();

        /** @var string $expectedResponse */
        $expectedResponse = json_encode(UserFactory::responseFromEntity($user));

        self::assertJsonStringEqualsJsonString(
            $expectedResponse,
            $actualResponse
        );
    }

    public function testPatch(): void
    {
        $userRequest = new UpdateUserRequest('henk@devries.nl', [Role::ROLE_ADMIN], 'password');
        $user        = new User('henk@devries.nl', [Role::ROLE_ADMIN]);

        $user->setPassword('hashed-password');

        $this->userFactory->updateEntityFromRequest($userRequest, $user)->willReturn($user);
        $this->entityManager->flush()->shouldBeCalled();

        /** @var string $actualResponse */
        $actualResponse = $this->userController->patch($userRequest, $user)->getContent();

        /** @var string $expectedResponse */
        $expectedResponse = json_encode(UserFactory::responseFromEntity($user));

        self::assertJsonStringEqualsJsonString(
            $expectedResponse,
            $actualResponse
        );
    }
}
