<?php

namespace App\Central\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Central\Entity\Tenant;

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
