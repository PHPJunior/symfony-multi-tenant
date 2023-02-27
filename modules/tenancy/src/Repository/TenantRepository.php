<?php

namespace Module\Tenancy\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Module\Tenancy\Entity\Tenant;

/**
 * @extends ServiceEntityRepository<Tenant>
 *
 * @method Tenant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tenant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tenant[]    findAll()
 * @method Tenant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TenantRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tenant::class);
    }

    public function save(Tenant $tenant): Tenant
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($tenant);
        $entityManager->flush();

        return $tenant;
    }
}
