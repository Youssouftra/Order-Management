<?php

namespace App\Repository;

use App\Entity\Burger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BurgerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Burger::class);
    }

    public function findPaginated(int $page, int $limit, ?string $search, ?bool $isActive)
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.typeProduit = :type')
            ->setParameter('type', 'BURGER')
            ->orderBy('b.nom', 'ASC')
            ->setMaxResults($limit);
        
        if ($search) {
            $qb->andWhere('b.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        if ($isActive !== null) {
            $qb->andWhere('b.disponible = :active')
               ->setParameter('active', $isActive);
        }
        
        return $qb->getQuery()->getResult();
    }
}
