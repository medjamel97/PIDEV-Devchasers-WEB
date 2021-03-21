<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210313214711 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE participant_conversation');
        $this->addSql('ALTER TABLE conversation ADD candidat_expediteur_id INT DEFAULT NULL, ADD id_candidat_destinataire INT NOT NULL');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9F19F0F9A FOREIGN KEY (candidat_expediteur_id) REFERENCES candidat (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9F19F0F9A ON conversation (candidat_expediteur_id)');
        $this->addSql('ALTER TABLE message ADD contenu VARCHAR(255) NOT NULL, ADD date_creation DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participant_conversation (id INT AUTO_INCREMENT NOT NULL, candidat_id INT DEFAULT NULL, conversation_id INT DEFAULT NULL, INDEX IDX_17A662BB9AC0396 (conversation_id), INDEX IDX_17A662BB8D0EB82 (candidat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE participant_conversation ADD CONSTRAINT FK_17A662BB8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE participant_conversation ADD CONSTRAINT FK_17A662BB9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9F19F0F9A');
        $this->addSql('DROP INDEX IDX_8A8E26E9F19F0F9A ON conversation');
        $this->addSql('ALTER TABLE conversation DROP candidat_expediteur_id, DROP id_candidat_destinataire');
        $this->addSql('ALTER TABLE message DROP contenu, DROP date_creation');
    }
}
