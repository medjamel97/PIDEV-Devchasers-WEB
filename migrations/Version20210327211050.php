<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210327211050 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_de_travail DROP FOREIGN KEY FK_81CF2B1DFCF77503');
        $this->addSql('DROP INDEX IDX_81CF2B1DFCF77503 ON offre_de_travail');
        $this->addSql('ALTER TABLE offre_de_travail DROP societe_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_de_travail ADD societe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offre_de_travail ADD CONSTRAINT FK_81CF2B1DFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('CREATE INDEX IDX_81CF2B1DFCF77503 ON offre_de_travail (societe_id)');
    }
}
