<?php

declare(strict_types=1);

namespace Tenant\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230111165939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->createTable('posts');
        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addColumn('title', Types::STRING, ['length' => 255]);
        $table->addColumn('created_at', Types::DATETIME_MUTABLE)->setNotnull(false);
        $table->addColumn('updated_at', Types::DATETIME_MUTABLE)->setNotnull(false);
        $table->setPrimaryKey(['id'], 'post_pk');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('posts');
    }
}
