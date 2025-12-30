<?php

namespace App\Repository;

use App\Entity\Complement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Complement>
 */
class ComplementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Complement::class);
    }

    public function findPaginated(int $page, int $limit, ?string $search = null, ?string $type = null, ?bool $isActive = null): Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC');

        if ($search) {
            $qb->andWhere('LOWER(c.nom) LIKE LOWER(:search) OR LOWER(c.description) LIKE LOWER(:search)')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($type) {
            $qb->andWhere('c.type = :type')
               ->setParameter('type', $type);
        }

        if ($isActive !== null) {
            $qb->andWhere('c.isActive = :isActive')
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

    public function findByType(string $type): array
    {
        return $this->findBy(['type' => $type, 'isActive' => true], ['nom' => 'ASC']);
    }
}
