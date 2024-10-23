<?php
declare(strict_types=1);

namespace App\Model\Response;

use OpenApi\Attributes as OA;

#[OA\Schema]
readonly class UserResponse
{
    public function __construct(
        public string $name,
    ) {
    }
}
