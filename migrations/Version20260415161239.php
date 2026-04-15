<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260415161239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create listings table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE listings (status VARCHAR(255) NOT NULL, id VARCHAR NOT NULL, seller_id VARCHAR NOT NULL, title VARCHAR NOT NULL, description VARCHAR(2000) NOT NULL, condition VARCHAR(255) NOT NULL, price_amount INT NOT NULL, price_currency VARCHAR(3) NOT NULL, PRIMARY KEY (id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE listings');
    }
}
