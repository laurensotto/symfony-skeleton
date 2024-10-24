<?php
declare(strict_types=1);

namespace App\Tests\Security;

use App\Exception\UserNotAvailableException;
use App\Security\UserProvider;
use App\Tests\Faker\UserFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[CoversClass(UserProvider::class)]
#[CoversClass(UserNotAvailableException::class)]
class UserProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<TokenStorageInterface>
     */
    private ObjectProphecy $tokenStorage;

    private UserProvider $userProvider;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->prophesize(TokenStorageInterface::class);

        $this->userProvider = new UserProvider($this->tokenStorage->reveal());
    }

    public function testGetUser(): void
    {
        $user  = UserFaker::create();
        $token = $this->prophesize(TokenInterface::class);

        $this->tokenStorage->getToken()->willReturn($token->reveal());
        $token->getUser()->willReturn($user);

        $foundUser = $this->userProvider->getUser();

        self::assertSame($user, $foundUser);
    }

    public function testGetUserThrowsWhenTokenNull(): void
    {
        $this->tokenStorage->getToken()->willReturn(null);

        $this->expectException(UserNotAvailableException::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->userProvider->getUser();
    }

    public function testGetUserThrowsWhenUserUnavailable(): void
    {
        $token = $this->prophesize(TokenInterface::class);

        $this->tokenStorage->getToken()->willReturn($token->reveal());
        $token->getUser()->willReturn(null);

        $this->expectException(UserNotAvailableException::class);
        $this->expectExceptionMessage('User not authenticated');
        $this->userProvider->getUser();
    }
}
