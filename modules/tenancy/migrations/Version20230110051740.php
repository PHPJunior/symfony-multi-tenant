<?php

declare(strict_types=1);

namespace Module\Tenancy\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230110051740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Tenant Table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('tenants');
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addColumn('subdomain', Types::STRING, ['length' => 255]);
        $table->addColumn('data', Types::JSON);
        $table->addColumn('created_at', Types::DATETIME_MUTABLE)->setNotnull(false);
        $table->addColumn('updated_at', Types::DATETIME_MUTABLE)->setNotnull(false);
        $table->setPrimaryKey(['id'], 'tenant_pk');
        $table->addUniqueIndex(['subdomain'], 'tenant_subdomain_unique');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('tenants');
    }
}
