<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\V1\UserController;
use App\Model\Response\User\UserResponse;
use App\Tests\Faker\UserFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserController::class)]
#[CoversClass(UserResponse::class)]
class UserControllerTest extends TestCase
{
    private UserController $userController;

    protected function setUp(): void
    {
        $this->userController = new UserController();
    }

    public function testGet(): void
    {
        $user = UserFaker::create();

        /** @var string $actualResponse */
        $actualResponse = $this->userController->get($user)->getContent();

        /** @var string $expectedResponse */
        $expectedResponse = json_encode(UserResponse::fromUser($user));

        self::assertJsonStringEqualsJsonString(
            $expectedResponse,
            $actualResponse
        );
    }
}
