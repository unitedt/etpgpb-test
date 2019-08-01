<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190801214238 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE entry (global_id INT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, kod VARCHAR(255) DEFAULT NULL, nomdescr VARCHAR(4096) DEFAULT NULL, idx VARCHAR(255) NOT NULL, razdel VARCHAR(255) NOT NULL, name VARCHAR(1024) NOT NULL, lft INT NOT NULL, lvl INT NOT NULL, rgt INT NOT NULL, PRIMARY KEY(global_id))');
        $this->addSql('CREATE INDEX idx_parent_id ON entry (parent_id)');
        $this->addSql('CREATE INDEX idx_tree_root ON entry (tree_root)');
        $this->addSql('CREATE INDEX idx_lft ON entry (lft)');
        $this->addSql('CREATE INDEX idx_rgt ON entry (rgt)');
        $this->addSql('CREATE INDEX idx_lvl ON entry (lvl)');
        $this->addSql('CREATE INDEX idx_idx ON entry (idx)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE entry');
    }
}
