<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260502000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add optimistic locking version column to listings table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE listings ADD version INT NOT NULL DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE listings DROP COLUMN version');
    }
}
