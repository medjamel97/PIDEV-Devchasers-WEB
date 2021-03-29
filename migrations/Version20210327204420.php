<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210327204420 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie ADD ho_ho_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634B50D25B2 FOREIGN KEY (ho_ho_id) REFERENCES offre_de_travail (id)');
        $this->addSql('CREATE INDEX IDX_497DD634B50D25B2 ON categorie (ho_ho_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634B50D25B2');
        $this->addSql('DROP INDEX IDX_497DD634B50D25B2 ON categorie');
        $this->addSql('ALTER TABLE categorie DROP ho_ho_id');
    }
}
