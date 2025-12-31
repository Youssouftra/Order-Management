<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.typeProduit = :type')
            ->andWhere('p.archived = false')
            ->andWhere('p.disponible = true')
            ->setParameter('type', $type)
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBurgers(): array
    {
        return $this->findByType('BURGER');
    }

    public function findComplements(): array
    {
        return $this->findByType('COMPLEMENT');
    }

    public function findMenus(): array
    {
        return $this->findByType('MENU');
    }
}
