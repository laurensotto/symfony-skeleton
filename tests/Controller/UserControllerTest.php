<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\V1\UserController;
use App\Model\Response\UserResponse;
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
        /** @var string $actualResponse */
        $actualResponse = $this->userController->get('test')->getContent();

        /** @var string $expectedResponse */
        $expectedResponse = json_encode(new UserResponse('test'));

        self::assertJsonStringEqualsJsonString(
            $expectedResponse,
            $actualResponse
        );
    }
}
