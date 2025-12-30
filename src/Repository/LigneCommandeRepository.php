<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneCommande>
 */
class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    public function getTopBurgers(int $limit = 5): array
    {
        return $this->createQueryBuilder('lc')
            ->select('b.id, b.nom, b.image, SUM(lc.quantite) as totalVentes')
            ->join('lc.burger', 'b')
            ->join('lc.commande', 'c')
            ->where('c.etat = :etat')
            ->setParameter('etat', Commande::ETAT_LIVREE)
            ->groupBy('b.id, b.nom, b.image')
            ->orderBy('totalVentes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getTopComplements(int $limit = 5): array
    {
        return $this->createQueryBuilder('lc')
            ->select('cp.id, cp.nom, cp.image, SUM(lc.quantite) as totalVentes')
            ->join('lc.complement', 'cp')
            ->join('lc.commande', 'c')
            ->where('c.etat = :etat')
            ->setParameter('etat', Commande::ETAT_LIVREE)
            ->groupBy('cp.id, cp.nom, cp.image')
            ->orderBy('totalVentes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getTopMenus(int $limit = 5): array
    {
        return $this->createQueryBuilder('lc')
            ->select('m.id, m.nom, m.image, SUM(lc.quantite) as totalVentes')
            ->join('lc.menu', 'm')
            ->join('lc.commande', 'c')
            ->where('c.etat = :etat')
            ->setParameter('etat', Commande::ETAT_LIVREE)
            ->groupBy('m.id, m.nom, m.image')
            ->orderBy('totalVentes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
