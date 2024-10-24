<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Exception\UserNotAvailableException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserProvider
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    /**
     * @throws UserNotAvailableException
     */
    public function getUser(): User
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            throw new UserNotAvailableException();
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new UserNotAvailableException();
        }

        return $user;
    }
}
