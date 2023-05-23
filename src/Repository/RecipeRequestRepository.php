<?php

namespace App\Repository;

use App\Entity\RecipeRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecipeRequest>
 *
 * @method RecipeRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeRequest[]    findAll()
 * @method RecipeRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeRequest::class);
    }

    public function save(RecipeRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RecipeRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return RecipeRequest[] Returns an array of RecipeRequest objects
     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneBySomeField($value): ?RecipeRequest
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countDayRequest($userId): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->where('e.user_id = :userId')
            ->andWhere('e.createdAt >= :startOfDay')
            ->setParameter('userId', $userId)
            ->setParameter('startOfDay', new \DateTime('today'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
