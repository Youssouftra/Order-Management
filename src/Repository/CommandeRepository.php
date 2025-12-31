<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findPaginated(int $page, int $limit, array $filters = []): Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC');

        if (!empty($filters['search'])) {
            $qb->andWhere('LOWER(c.clientNom) LIKE LOWER(:search) OR c.clientTelephone LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['etat'])) {
            $qb->andWhere('c.statut = :statut')
               ->setParameter('statut', strtoupper($filters['etat']));
        }

        $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        return new Paginator($qb, false);
    }

    public function countByEtat(string $etat): int
    {
        return $this->count(['statut' => strtoupper($etat)]);
    }

    public function countEnCours(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statut IN (:etats)')
            ->setParameter('etats', ['EN_ATTENTE', 'EN_PREPARATION', 'PRETE', 'EN_LIVRAISON'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRecetteJournaliere(\DateTimeInterface $date): string
    {
        $debut = (new \DateTime($date->format('Y-m-d')))->setTime(0, 0, 0);
        $fin = (new \DateTime($date->format('Y-m-d')))->setTime(23, 59, 59);

        $result = $this->createQueryBuilder('c')
            ->select('SUM(c.montantTotal) as total')
            ->where('c.statut = :statut')
            ->andWhere('c.dateCommande BETWEEN :debut AND :fin')
            ->setParameter('statut', 'TERMINEE')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ?? '0.00';
    }

    public function findRecent(int $limit = 10): array
    {
        try {
            return $this->createQueryBuilder('c')
                ->leftJoin('c.client', 'cl')
                ->addSelect('cl')
                ->orderBy('c.id', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function findForZoneView(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(50);

        if (!empty($filters['etat'])) {
            $qb->andWhere('c.statut = :statut')
               ->setParameter('statut', strtoupper($filters['etat']));
        }

        return $qb->getQuery()->getResult();
    }
    
    public function findByLivreur(int $livreurId): array
    {
        return $this->findBy(['livreur' => $livreurId], ['id' => 'DESC']);
    }
}
