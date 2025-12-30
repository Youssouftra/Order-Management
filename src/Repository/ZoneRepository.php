<?php

namespace App\Repository;

use App\Entity\Zone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Zone>
 */
class ZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zone::class);
    }

    public function findPaginated(int $page, int $limit, ?string $search = null, ?bool $isActive = null): Paginator
    {
        $qb = $this->createQueryBuilder('z')
            ->orderBy('z.nom', 'ASC');

        if ($search) {
            $qb->andWhere('LOWER(z.nom) LIKE LOWER(:search) OR LOWER(z.description) LIKE LOWER(:search)')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($isActive !== null) {
            $qb->andWhere('z.isActive = :isActive')
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
