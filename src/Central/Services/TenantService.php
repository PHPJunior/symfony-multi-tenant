<?php

namespace App\Central\Services;

use App\Central\Doctrine\DBAL\TenantConnection;
use App\Central\Entity\Tenant;
use App\Tenant\Entity\User;
use App\Tenant\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TenantService
{
    public function __construct(
        private readonly EntityManager $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
    ){
    }

    /**
     * @param Tenant $tenant
     * @return void
     * @throws Exception
     */
    public function switchTenant(Tenant $tenant): void
    {
        /** @var TenantConnection $connection */
        $connection = $this->getConnection();
        $connection->changeDatabase($tenant->getDbname());
    }

    /**
     * @return TenantConnection|Connection
     */
    public function getConnection(): Connection|TenantConnection
    {
        return $this->em->getConnection();
    }

    /**
     * @param string $email
     * @param string $plaintextPassword
     * @return void
     * @throws \Exception
     */
    public function createUser(string $email, string $plaintextPassword): void
    {
        if ($this->userRepository->findOneBy(['email' => $email])) {
            throw new \Exception('User already exists');
        }
        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $this->userRepository->save($user, true);
    }
}
