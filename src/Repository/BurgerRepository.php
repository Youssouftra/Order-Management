<?php

namespace App\Repository;

use App\Entity\Burger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Burger>
 */
class BurgerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Burger::class);
    }

    public function findPaginated(int $page, int $limit, ?string $search = null, ?bool $isActive = null): Paginator
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'DESC');

        if ($search) {
            $qb->andWhere('LOWER(b.nom) LIKE LOWER(:search) OR LOWER(b.description) LIKE LOWER(:search)')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($isActive !== null) {
            $qb->andWhere('b.isActive = :isActive')
               ->setParameter('isActive', $isActive);
        }

        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return new Paginator($qb);
    }

    public function countAll(): int
    {
        return $this->count([]);
    }

    public function countActive(): int
    {
        return $this->count(['isActive' => true]);
    }

    public function findAllActive(): array
    {
        return $this->findBy(['isActive' => true], ['nom' => 'ASC']);
    }
}
