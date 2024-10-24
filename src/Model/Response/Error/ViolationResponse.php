<?php
declare(strict_types=1);

namespace App\Model\Response\Error;

use OpenApi\Attributes as OA;

#[OA\Schema]
readonly class ViolationResponse
{
    public function __construct(
        #[OA\Property(property: 'propertyPath', type: 'string', example: 'email')]
        public string $propertyPath,
        #[OA\Property(property: 'message', type: 'string', example: 'Please submit an email.')]
        public string $message,
        #[OA\Property(property: 'value', type: 'string', example: 'henkdevries.nl')]
        public string $value,
    ) {
    }
}
