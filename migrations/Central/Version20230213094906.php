<?php

declare(strict_types=1);

namespace Central\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213094906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create User Table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('users');
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addColumn('email', Types::STRING, ['length' => 255]);
        $table->addColumn('password', Types::STRING, ['length' => 255]);
        $table->addColumn('created_at', Types::DATETIMETZ_IMMUTABLE)->setNotnull(false);
        $table->addColumn('updated_at', Types::DATETIMETZ_IMMUTABLE)->setNotnull(false);
        $table->setPrimaryKey(['id'], 'user_pk');
        $table->addUniqueIndex(['email'], 'user_email_unique');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
