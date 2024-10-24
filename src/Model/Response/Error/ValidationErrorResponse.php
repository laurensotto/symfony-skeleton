<?php
declare(strict_types=1);

namespace App\Model\Response\Error;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

#[OA\Schema]
readonly class ValidationErrorResponse
{
    /**
     * @param ViolationResponse[] $violations
     */
    public function __construct(
        #[OA\Property(
            property: 'violations',
            type: 'array',
            items: new OA\Items(ref: new Model(type: ViolationResponse::class))
        )]
        public array $violations
    ) {
    }
}
