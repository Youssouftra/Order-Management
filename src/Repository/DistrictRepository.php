<?php

namespace App\Repository;

use App\Entity\District;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<District>
 */
class DistrictRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, District::class);
    }

    public function findByZone(int $zoneId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.zone = :zoneId')
            ->setParameter('zoneId', $zoneId)
            ->orderBy('d.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(District $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(District $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
