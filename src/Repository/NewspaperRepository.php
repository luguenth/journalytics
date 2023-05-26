<?php

namespace App\Repository;

use App\Entity\Newspaper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Newspaper>
 *
 * @method Newspaper|null find($id, $lockMode = null, $lockVersion = null)
 * @method Newspaper|null findOneBy(array $criteria, array $orderBy = null)
 * @method Newspaper[]    findAll()
 * @method Newspaper[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewspaperRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newspaper::class);
    }

    public function save(Newspaper $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Newspaper $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllNewspapersFromJournalist(int $id): array
{
    $query = $this->createQueryBuilder('n')
        ->select('n')
        ->leftJoin('n.articles', 'a')
        ->leftJoin('a.journalists', 'j')
        ->where('j.id = :id')
        ->setParameter('id', $id)
        ->getQuery();

    return $query->getResult();
}

//    /**
//     * @return Newspaper[] Returns an array of Newspaper objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Newspaper
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
