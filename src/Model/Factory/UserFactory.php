<?php
declare(strict_types=1);

namespace App\Model\Factory;

use App\Entity\User;
use App\Model\Request\User\CreateUserRequest;
use App\Model\Request\User\UpdateUserRequest;
use App\Model\Response\User\UserResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function entityFromRequest(CreateUserRequest $request): User
    {
        $user = new User(
            $request->email,
            $request->roles
        );

        return $user->setPassword($this->passwordHasher->hashPassword($user, $request->password));
    }

    public function updateEntityFromRequest(UpdateUserRequest $request, User $user): User
    {
        $user
            ->setEmail($request->email)
            ->setRoles($request->roles);

        if ($request->password) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $request->password));
        }

        return $user;
    }

    public static function responseFromEntity(User $user): UserResponse
    {
        return new UserResponse(
            $user->getEmail()
        );
    }
}
