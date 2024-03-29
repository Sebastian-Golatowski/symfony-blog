<?php

namespace App\Repository;

use App\Entity\Report;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Report>
 *
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function save(Report $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Report $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Report[] Returns an array of Report objects
//     */
    public function isAlreadyReported($userId, $postId)
    {
        $qb = $this->createQueryBuilder('e');

        $qb ->where('e.post = :postId')
            ->setParameter('postId', $postId)
            ->andWhere('e.user = :userId')
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getResult();
    }

    public function countReports($number)
    {
        $qb = $this->createQueryBuilder('r');
        $qb ->select('IDENTITY(r.post) AS postId, COUNT(r) AS reportCount, p.title, u.username')
            ->innerJoin(Post::class, 'p', 'WITH', 'r.post = p.id')
            ->innerJoin(User::class , 'u' , 'WITH', 'p.user = u.id')
            ->groupBy('postId')
            ->having('reportCount >= :number')
            ->setParameter('number', $number)
            ->OrderBy('reportCount','DESC');
    
        return $qb->getQuery()->getResult();
    }
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Report
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
