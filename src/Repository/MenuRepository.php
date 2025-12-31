<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findPaginated(int $page, int $limit, ?string $search, ?bool $isActive)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.typeProduit = :type')
            ->setParameter('type', 'MENU')
            ->orderBy('m.nom', 'ASC')
            ->setMaxResults($limit);
        
        if ($search) {
            $qb->andWhere('m.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        if ($isActive !== null) {
            $qb->andWhere('m.disponible = :active')
               ->setParameter('active', $isActive);
        }
        
        return $qb->getQuery()->getResult();
    }
}
