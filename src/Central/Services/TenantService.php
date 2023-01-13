<?php

namespace App\Central\Services;

use App\Central\Entity\Tenant;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

class TenantService
{
    public function __construct(
        private readonly ManagerRegistry $doctrine
    ){
    }

    /**
     * @param Tenant $tenant
     * @return void
     *
     * @throws Exception
     */
    public function createDatabaseForTenant(Tenant $tenant): void
    {
        $connectionName = $this->getDoctrine()->getDefaultConnectionName();
        $connection = $this->getDoctrineConnection($connectionName);
        $params = $connection->getParams();
        unset($params['dbname'], $params['path'], $params['url']);
        $tmpConnection = DriverManager::getConnection($params);

        $schemaManager = method_exists($tmpConnection, 'createSchemaManager') ? $tmpConnection->createSchemaManager() : $tmpConnection->getSchemaManager();

        $shouldNotCreateDatabase = in_array($tenant->getDbname(), $schemaManager->listDatabases());
        if ($shouldNotCreateDatabase) {
            throw new \InvalidArgumentException("Database already exists.");
        }

        $schemaManager->createDatabase($tenant->getDbname());
    }

    /**
     * @return ManagerRegistry
     */
    private function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }

    /**
     * @param string $connectionName
     *
     * @return object
     */
    private function getDoctrineConnection(string $connectionName): object
    {
        return $this->getDoctrine()->getConnection($connectionName);
    }
}
