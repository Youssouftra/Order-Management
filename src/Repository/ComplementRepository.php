<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ComplementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findPaginated(int $page, int $limit, ?string $search, ?bool $isActive, ?string $type)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.typeProduit = :typeProduit')
            ->setParameter('typeProduit', 'COMPLEMENT')
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
        
        if ($type) {
            $qb->andWhere('p.typeComplement = :type')
               ->setParameter('type', $type);
        }
        
        return $qb->getQuery()->getResult();
    }
}
