<?php

namespace Module\ResetPassword\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Module\ResetPassword\Entity\ResetPassword;

/**
 * @extends ServiceEntityRepository<ResetPassword>
 *
 * @method ResetPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResetPassword[]    findAll()
 * @method ResetPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResetPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPassword::class);
    }

    public function save(ResetPassword $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ResetPassword $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
