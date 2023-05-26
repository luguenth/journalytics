<?php

namespace App\Repository;

use App\Entity\Journalist;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Journalist>
 *
 * @method Journalist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Journalist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Journalist[]    findAll()
 * @method Journalist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Journalist::class);
    }

    public function save(Journalist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Journalist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllJournalistsFromNewspaper(int $id): array
    {
        $query = $this->createQueryBuilder('j')
            ->select('j')
            ->leftJoin('j.articles', 'a')
            ->leftJoin('a.newspapers', 'n')
            ->where('n.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getResult();
    }
}
