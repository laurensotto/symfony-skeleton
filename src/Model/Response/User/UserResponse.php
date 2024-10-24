<?php
declare(strict_types=1);

namespace App\Model\Response\User;

use App\Entity\User;
use OpenApi\Attributes as OA;

#[OA\Schema]
readonly class UserResponse
{
    public function __construct(
        public string $email,
    ) {
    }

    public static function fromUser(User $user): UserResponse
    {
        return new UserResponse($user->getEmail());
    }
}
