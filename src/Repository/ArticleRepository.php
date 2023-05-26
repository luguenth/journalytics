<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllArticlesWithAuthors(): array
    {
        //Fetch all the articles which really do have an author
        // and sort them by time, newest first
        $query = $this->createQueryBuilder('a')
            ->innerJoin('a.journalists', 'j')
            ->addSelect('j')
            ->orderBy('a.date', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    public function getAllArticlesFromNewspaper($id): array 
    {
        $query = $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.newspapers', 'n')
            ->where('n.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getResult();
    }
}
