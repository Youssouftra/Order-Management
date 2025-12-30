<?php

namespace App\Repository;

use App\Entity\Livreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livreur>
 */
class LivreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livreur::class);
    }

    public function findPaginated(int $page, int $limit, ?string $search = null, ?string $statut = null, ?bool $isActive = null): Paginator
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.zone', 'z')
            ->addSelect('z')
            ->orderBy('l.nom', 'ASC');

        if ($search) {
            $qb->andWhere('LOWER(l.nom) LIKE LOWER(:search) OR LOWER(l.prenom) LIKE LOWER(:search) OR l.telephone LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($statut) {
            $qb->andWhere('l.statut = :statut')
               ->setParameter('statut', $statut);
        }

        if ($isActive !== null) {
            $qb->andWhere('l.isActive = :isActive')
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

    public function countByStatut(string $statut): int
    {
        return $this->count(['statut' => $statut, 'isActive' => true]);
    }

    public function findAllActive(): array
    {
        return $this->findBy(['isActive' => true], ['nom' => 'ASC']);
    }

    public function findDisponibles(): array
    {
        return $this->findBy([
            'statut' => Livreur::STATUT_DISPONIBLE, 
            'isActive' => true
        ], ['nom' => 'ASC']);
    }

    public function findByZone(int $zoneId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.zone = :zoneId')
            ->andWhere('l.isActive = true')
            ->setParameter('zoneId', $zoneId)
            ->orderBy('l.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
