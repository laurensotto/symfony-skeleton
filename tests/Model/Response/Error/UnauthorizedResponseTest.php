<?php
declare(strict_types=1);

namespace App\Tests\Model\Response\Error;

use App\Model\Response\Error\UnauthorizedResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnauthorizedResponse::class)]
class UnauthorizedResponseTest extends TestCase
{
    public function testConstruct(): void
    {
        $response = new UnauthorizedResponse(
            401,
            'You are not authorized.',
        );

        self::assertSame(401, $response->code);
        self::assertSame('You are not authorized.', $response->message);
    }
}
