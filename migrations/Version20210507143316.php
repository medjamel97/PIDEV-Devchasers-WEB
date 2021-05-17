<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210507143316 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E938DBC740');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9F19F0F9A');
        $this->addSql('DROP INDEX IDX_8A8E26E938DBC740 ON conversation');
        $this->addSql('DROP INDEX IDX_8A8E26E9F19F0F9A ON conversation');
        $this->addSql('ALTER TABLE conversation ADD user_expediteur_id INT DEFAULT NULL, ADD user_destinataire_id INT DEFAULT NULL, DROP candidat_expediteur_id, DROP candidat_destinataire_id');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9FEBDF231 FOREIGN KEY (user_expediteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9B62423E1 FOREIGN KEY (user_destinataire_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9FEBDF231 ON conversation (user_expediteur_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9B62423E1 ON conversation (user_destinataire_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9FEBDF231');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9B62423E1');
        $this->addSql('DROP INDEX IDX_8A8E26E9FEBDF231 ON conversation');
        $this->addSql('DROP INDEX IDX_8A8E26E9B62423E1 ON conversation');
        $this->addSql('ALTER TABLE conversation ADD candidat_expediteur_id INT DEFAULT NULL, ADD candidat_destinataire_id INT DEFAULT NULL, DROP user_expediteur_id, DROP user_destinataire_id');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E938DBC740 FOREIGN KEY (candidat_destinataire_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9F19F0F9A FOREIGN KEY (candidat_expediteur_id) REFERENCES candidat (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E938DBC740 ON conversation (candidat_destinataire_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9F19F0F9A ON conversation (candidat_expediteur_id)');
    }
}
