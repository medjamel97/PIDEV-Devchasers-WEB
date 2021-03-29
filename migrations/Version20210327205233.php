<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210327205233 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634F14D7DA');
        $this->addSql('DROP INDEX IDX_497DD634F14D7DA ON categorie');
        $this->addSql('ALTER TABLE categorie DROP offre_de_travail_id');
        $this->addSql('ALTER TABLE offre_de_travail ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offre_de_travail ADD CONSTRAINT FK_81CF2B1DBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_81CF2B1DBCF5E72D ON offre_de_travail (categorie_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie ADD offre_de_travail_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634F14D7DA FOREIGN KEY (offre_de_travail_id) REFERENCES offre_de_travail (id)');
        $this->addSql('CREATE INDEX IDX_497DD634F14D7DA ON categorie (offre_de_travail_id)');
        $this->addSql('ALTER TABLE offre_de_travail DROP FOREIGN KEY FK_81CF2B1DBCF5E72D');
        $this->addSql('DROP INDEX IDX_81CF2B1DBCF5E72D ON offre_de_travail');
        $this->addSql('ALTER TABLE offre_de_travail DROP categorie_id');
    }
}
