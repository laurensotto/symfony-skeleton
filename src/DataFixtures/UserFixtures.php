<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Constant\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User(
            'user@test.nl',
            [Role::ROLE_USER]
        );

        $user->setPassword($this->passwordHasher->hashPassword($user, 'test'));
        $manager->persist($user);

        $admin = new User(
            'admin@test.nl',
            [Role::ROLE_ADMIN]
        );

        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'test'));
        $manager->persist($admin);

        $manager->flush();
    }
}
