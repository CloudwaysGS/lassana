<?php

namespace App\Repository;

use App\Entity\Depot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depot>
 *
 * @method Depot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depot[]    findAll()
 * @method Depot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depot::class);
    }

    public function save(Depot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Depot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySearchTerm($searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('d');

        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('d.libelle LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return $queryBuilder->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Depot[] Returns an array of Depot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Depot
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
