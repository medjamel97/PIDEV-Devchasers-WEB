<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210330085559 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('ALTER TABLE candidat CHANGE tel tel VARCHAR(8) NOT NULL');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EFCF77503');
        $this->addSql('DROP INDEX IDX_B26681EFCF77503 ON evenement');
        $this->addSql('ALTER TABLE evenement ADD debut DATETIME NOT NULL, ADD fin DATETIME NOT NULL, ADD descp VARCHAR(255) NOT NULL, ADD all_day TINYINT(1) NOT NULL, ADD id_user INT NOT NULL, DROP societe_id, CHANGE description titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFFCF77503');
        $this->addSql('DROP INDEX IDX_404021BFFCF77503 ON formation');
        $this->addSql('ALTER TABLE formation ADD debut DATETIME NOT NULL, ADD fin DATETIME NOT NULL, ADD id_user INT NOT NULL, DROP societe_id, CHANGE description filiere VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX IDX_B6BD307F9AC0396 ON message');
        $this->addSql('ALTER TABLE message ADD candidat_destinataire_id INT DEFAULT NULL, DROP est_proprietaire, DROP est_vu, CHANGE contenu contenu VARCHAR(255) DEFAULT NULL, CHANGE date_creation date_creation DATE NOT NULL, CHANGE conversation_id candidat_expediteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF19F0F9A FOREIGN KEY (candidat_expediteur_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F38DBC740 FOREIGN KEY (candidat_destinataire_id) REFERENCES candidat (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF19F0F9A ON message (candidat_expediteur_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F38DBC740 ON message (candidat_destinataire_id)');
        $this->addSql('ALTER TABLE societe CHANGE num_tel_societe num_tel_societe VARCHAR(8) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, candidat_expediteur_id INT DEFAULT NULL, candidat_destinataire_id INT DEFAULT NULL, date_dernier_message DATETIME DEFAULT NULL, INDEX IDX_8A8E26E938DBC740 (candidat_destinataire_id), INDEX IDX_8A8E26E9F19F0F9A (candidat_expediteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E938DBC740 FOREIGN KEY (candidat_destinataire_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9F19F0F9A FOREIGN KEY (candidat_expediteur_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE candidat CHANGE tel tel VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE evenement ADD societe_id INT DEFAULT NULL, ADD description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP titre, DROP debut, DROP fin, DROP descp, DROP all_day, DROP id_user');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('CREATE INDEX IDX_B26681EFCF77503 ON evenement (societe_id)');
        $this->addSql('ALTER TABLE formation ADD societe_id INT DEFAULT NULL, DROP debut, DROP fin, DROP id_user, CHANGE filiere description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('CREATE INDEX IDX_404021BFFCF77503 ON formation (societe_id)');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF19F0F9A');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F38DBC740');
        $this->addSql('DROP INDEX IDX_B6BD307FF19F0F9A ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F38DBC740 ON message');
        $this->addSql('ALTER TABLE message ADD conversation_id INT DEFAULT NULL, ADD est_proprietaire TINYINT(1) NOT NULL, ADD est_vu TINYINT(1) NOT NULL, DROP candidat_expediteur_id, DROP candidat_destinataire_id, CHANGE contenu contenu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE date_creation date_creation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
        $this->addSql('ALTER TABLE societe CHANGE num_tel_societe num_tel_societe INT NOT NULL');
    }
}
