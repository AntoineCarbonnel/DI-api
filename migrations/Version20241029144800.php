<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241029144800 extends AbstractMigration
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
        $this->addSql('CREATE TABLE purchases (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_id INTEGER NOT NULL, product_id VARCHAR(255) NOT NULL, purchase_identifier VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, price INTEGER NOT NULL, currency VARCHAR(3) NOT NULL, date DATETIME NOT NULL, CONSTRAINT FK_AA6431FE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO purchases (id, customer_id, product_id, purchase_identifier, quantity, price, currency, date) SELECT id, customer_id, product_id, purchase_identifier, quantity, price, currency, date FROM __temp__purchases');
        $this->addSql('DROP TABLE __temp__purchases');
        $this->addSql('CREATE INDEX IDX_AA6431FE9395C3F3 ON purchases (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__purchases AS SELECT id, customer_id, product_id, purchase_identifier, quantity, price, currency, date FROM purchases');
        $this->addSql('DROP TABLE purchases');
        $this->addSql('CREATE TABLE purchases (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_id INTEGER DEFAULT NULL, product_id VARCHAR(255) NOT NULL, purchase_identifier VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, price INTEGER NOT NULL, currency VARCHAR(3) NOT NULL, date DATETIME NOT NULL, CONSTRAINT FK_AA6431FE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO purchases (id, customer_id, product_id, purchase_identifier, quantity, price, currency, date) SELECT id, customer_id, product_id, purchase_identifier, quantity, price, currency, date FROM __temp__purchases');
        $this->addSql('DROP TABLE __temp__purchases');
        $this->addSql('CREATE INDEX IDX_AA6431FE9395C3F3 ON purchases (customer_id)');
    }
}
