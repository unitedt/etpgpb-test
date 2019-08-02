<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190802005136 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE entry (global_id INT NOT NULL, parent_id INT DEFAULT NULL, Kod VARCHAR(255) DEFAULT NULL, Nomdescr VARCHAR(4096) DEFAULT NULL, Idx VARCHAR(255) NOT NULL, Razdel VARCHAR(255) NOT NULL, Name VARCHAR(1024) NOT NULL, path VARCHAR(3000) DEFAULT NULL, lvl INT DEFAULT NULL, PRIMARY KEY(global_id))');
        $this->addSql('CREATE INDEX IDX_2B219D70727ACA70 ON entry (parent_id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70727ACA70 FOREIGN KEY (parent_id) REFERENCES entry (global_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE entry DROP CONSTRAINT FK_2B219D70727ACA70');
        $this->addSql('DROP TABLE entry');
    }
}
