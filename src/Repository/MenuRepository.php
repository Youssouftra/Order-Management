<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findPaginated(int $page, int $limit, ?string $search, ?bool $isActive)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.typeProduit = :type')
            ->setParameter('type', 'MENU')
            ->orderBy('p.nom', 'ASC')
            ->setMaxResults($limit);
        
        if ($search) {
            $qb->andWhere('p.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        if ($isActive !== null) {
            $qb->andWhere('p.disponible = :active AND p.archived = :archived')
               ->setParameter('active', $isActive)
               ->setParameter('archived', false);
        }
        
        return $qb->getQuery()->getResult();
    }
}
