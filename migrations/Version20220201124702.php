<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220201124702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer CHANGE statut statut TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE statut statut INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room CHANGE statut statut TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE sous_menus CHANGE id_menu id_menu INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer CHANGE statut statut TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE reservation CHANGE statut statut INT DEFAULT 0');
        $this->addSql('ALTER TABLE room CHANGE statut statut TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE sous_menus CHANGE id_menu id_menu INT DEFAULT NULL');
    }
}
