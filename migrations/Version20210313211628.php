<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210313211628 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E99D1C3019');
        $this->addSql('DROP INDEX IDX_8A8E26E99D1C3019 ON conversation');
        $this->addSql('ALTER TABLE conversation DROP participant_id');
        $this->addSql('ALTER TABLE message DROP contenu, DROP date_creation');
        $this->addSql('ALTER TABLE participant_conversation CHANGE candidat_id candidat_id INT DEFAULT NULL, CHANGE conversation_id conversation_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation ADD participant_id INT NOT NULL');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E99D1C3019 FOREIGN KEY (participant_id) REFERENCES candidat (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E99D1C3019 ON conversation (participant_id)');
        $this->addSql('ALTER TABLE message ADD contenu VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD date_creation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE participant_conversation CHANGE candidat_id candidat_id INT NOT NULL, CHANGE conversation_id conversation_id INT NOT NULL');
    }
}
