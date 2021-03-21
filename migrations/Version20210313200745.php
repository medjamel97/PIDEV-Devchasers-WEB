<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210313200745 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, participant_id INT NOT NULL, date_dernier_message DATETIME NOT NULL, INDEX IDX_8A8E26E99D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant_conversation (id INT AUTO_INCREMENT NOT NULL, candidat_id INT NOT NULL, conversation_id INT NOT NULL, INDEX IDX_17A662BB8D0EB82 (candidat_id), INDEX IDX_17A662BB9AC0396 (conversation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E99D1C3019 FOREIGN KEY (participant_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE participant_conversation ADD CONSTRAINT FK_17A662BB8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE participant_conversation ADD CONSTRAINT FK_17A662BB9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F38DBC740');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF19F0F9A');
        $this->addSql('DROP INDEX IDX_B6BD307F38DBC740 ON message');
        $this->addSql('DROP INDEX IDX_B6BD307FF19F0F9A ON message');
        $this->addSql('ALTER TABLE message ADD conversation_id INT DEFAULT NULL, DROP candidat_expediteur_id, DROP candidat_destinataire_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE participant_conversation DROP FOREIGN KEY FK_17A662BB9AC0396');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE participant_conversation');
        $this->addSql('DROP INDEX IDX_B6BD307F9AC0396 ON message');
        $this->addSql('ALTER TABLE message ADD candidat_destinataire_id INT DEFAULT NULL, CHANGE conversation_id candidat_expediteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F38DBC740 FOREIGN KEY (candidat_destinataire_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF19F0F9A FOREIGN KEY (candidat_expediteur_id) REFERENCES candidat (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F38DBC740 ON message (candidat_destinataire_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF19F0F9A ON message (candidat_expediteur_id)');
    }
}
