<?php

namespace App\Repository;

use App\Entity\Complement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ComplementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Complement::class);
    }

    public function findPaginated(int $page, int $limit, ?string $search, ?bool $isActive, ?string $type)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.typeProduit = :typeProduit')
            ->setParameter('typeProduit', 'COMPLEMENT')
            ->orderBy('c.nom', 'ASC')
            ->setMaxResults($limit);
        
        if ($search) {
            $qb->andWhere('c.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        if ($isActive !== null) {
            $qb->andWhere('c.disponible = :active')
               ->setParameter('active', $isActive);
        }
        
        if ($type) {
            $qb->andWhere('c.typeComplement = :type')
               ->setParameter('type', $type);
        }
        
        return $qb->getQuery()->getResult();
    }
}
