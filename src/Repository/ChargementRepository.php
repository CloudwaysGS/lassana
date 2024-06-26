<?php

namespace App\Repository;

use App\Entity\Chargement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chargement>
 *
 * @method Chargement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chargement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chargement[]    findAll()
 * @method Chargement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chargement::class);
    }

    public function save(Chargement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Chargement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrderedByDate()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByName($nom)
    {
        $results = $this->createQueryBuilder('c')
            ->andWhere('c.nomClient LIKE :searchTerm OR c.numeroFacture LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$nom.'%')
            ->orderBy('c.nomClient', 'ASC')
            ->getQuery()
            ->getResult();

        if ($results === false || empty($results)) {
            // Gérer le cas où aucun résultat n'est trouvé
            return []; // Ou vous pouvez lever une exception ou retourner null selon votre logique
        }

        return $results;
    }

    public function getTotalChargements(): float
    {
        return $this->createQueryBuilder('c')
            ->select('COALESCE(SUM(c.total), 0) AS totalChargements')
            ->getQuery()
            ->getSingleScalarResult();
    }


//    /**
//     * @return Chargement[] Returns an array of Chargement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Chargement
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
