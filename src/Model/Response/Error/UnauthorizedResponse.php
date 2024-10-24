<?php
declare(strict_types=1);

namespace App\Model\Response\Error;

use OpenApi\Attributes as OA;

#[OA\Schema]
readonly class UnauthorizedResponse
{
    public function __construct(
        #[OA\Property(
            property: 'code',
            type: 'integer',
            example: 401,
        )]
        public int $code,
        #[OA\Property(
            property: 'message',
            type: 'string',
            example: 'Invalid credentials.',
        )]
        public string $message,
    ) {
    }
}
