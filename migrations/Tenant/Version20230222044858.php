<?php

namespace Tenant\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

class Version20230222044858 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('reset_password');
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addColumn('user_id', Types::INTEGER)->setNotnull(false);
        $table->addColumn('hashed_token', Types::TEXT, ['length' => 255])->setNotnull(true);
        $table->addColumn('requested_at', Types::DATETIME_IMMUTABLE)->setNotnull(false);
        $table->addColumn('expires_at', Types::DATETIME_IMMUTABLE)->setNotnull(false);
        $table->setPrimaryKey(['id'], 'reset_password_pk');
        $table->addIndex(['user_id'], 'reset_password_user_id_idx');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('reset_password');
    }
}
