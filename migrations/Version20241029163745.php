<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241029163745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__purchases AS SELECT id, customer_id, product_id, purchase_identifier, quantity, price, currency, date FROM purchases');
        $this->addSql('DROP TABLE purchases');
        $this->addSql('CREATE TABLE purchases (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_id INTEGER NOT NULL, product_id VARCHAR(255) NOT NULL, purchase_identifier VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, price INTEGER NOT NULL, currency VARCHAR(3) NOT NULL, date DATETIME NOT NULL)');
        $this->addSql('INSERT INTO purchases (id, customer_id, product_id, purchase_identifier, quantity, price, currency, date) SELECT id, customer_id, product_id, purchase_identifier, quantity, price, currency, date FROM __temp__purchases');
        $this->addSql('DROP TABLE __temp__purchases');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__purchases AS SELECT id, product_id, purchase_identifier, customer_id, quantity, price, currency, date FROM purchases');
        $this->addSql('DROP TABLE purchases');
        $this->addSql('CREATE TABLE purchases (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id VARCHAR(255) NOT NULL, purchase_identifier VARCHAR(255) NOT NULL, customer_id VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, price INTEGER NOT NULL, currency VARCHAR(3) NOT NULL, date DATETIME NOT NULL)');
        $this->addSql('INSERT INTO purchases (id, product_id, purchase_identifier, customer_id, quantity, price, currency, date) SELECT id, product_id, purchase_identifier, customer_id, quantity, price, currency, date FROM __temp__purchases');
        $this->addSql('DROP TABLE __temp__purchases');
    }
}
