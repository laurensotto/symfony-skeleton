<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 * @method User|null find(mixed $id, LockMode|int|null $lockMode = null, int|null $lockVersion = null)
 * @method User[]    findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method User|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method User[]    findAll()
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}
