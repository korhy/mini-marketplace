<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260421145510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create wallets and transactions tables for wallet management'.PHP_EOL.
            ' - Wallets table to store user balances'.PHP_EOL.
            ' - Transactions table to record all wallet transactions (credits and debits)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transactions (id UUID NOT NULL, type VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description VARCHAR(255) NOT NULL, amount_amount INT NOT NULL, amount_currency VARCHAR(3) NOT NULL, wallet_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_EAA81A4C712520F3 ON transactions (wallet_id)');
        $this->addSql('CREATE TABLE wallets (id UUID NOT NULL, balance_amount INT NOT NULL, balance_currency VARCHAR(3) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C712520F3 FOREIGN KEY (wallet_id) REFERENCES wallets (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transactions DROP CONSTRAINT FK_EAA81A4C712520F3');
        $this->addSql('DROP TABLE transactions');
        $this->addSql('DROP TABLE wallets');
    }
}
