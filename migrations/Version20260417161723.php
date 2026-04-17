<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260417161723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE orders (id VARCHAR NOT NULL, buyer_id VARCHAR NOT NULL, listing_id VARCHAR NOT NULL, status VARCHAR(255) NOT NULL, total_price_amount INT NOT NULL, total_price_currency VARCHAR(3) NOT NULL, PRIMARY KEY (id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE orders');
    }
}
