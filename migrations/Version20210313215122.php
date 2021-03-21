<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210313215122 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation ADD candidat_destinataire_id INT DEFAULT NULL, DROP id_candidat_destinataire');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E938DBC740 FOREIGN KEY (candidat_destinataire_id) REFERENCES candidat (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E938DBC740 ON conversation (candidat_destinataire_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E938DBC740');
        $this->addSql('DROP INDEX IDX_8A8E26E938DBC740 ON conversation');
        $this->addSql('ALTER TABLE conversation ADD id_candidat_destinataire INT NOT NULL, DROP candidat_destinataire_id');
    }
}
