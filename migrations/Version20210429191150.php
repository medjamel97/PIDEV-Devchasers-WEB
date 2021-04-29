<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210429191150 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature_mission DROP FOREIGN KEY FK_7ADE8DEA8D0EB82');
        $this->addSql('ALTER TABLE candidature_mission DROP FOREIGN KEY FK_7ADE8DEABE6CAE90');
        $this->addSql('ALTER TABLE candidature_mission ADD CONSTRAINT FK_7ADE8DEA8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE candidature_mission ADD CONSTRAINT FK_7ADE8DEABE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE candidature_offre DROP FOREIGN KEY FK_91FCEF3B8D0EB82');
        $this->addSql('ALTER TABLE candidature_offre DROP FOREIGN KEY FK_91FCEF3BF14D7DA');
        $this->addSql('DROP INDEX id ON candidature_offre');
        $this->addSql('ALTER TABLE candidature_offre ADD CONSTRAINT FK_91FCEF3B8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE candidature_offre ADD CONSTRAINT FK_91FCEF3BF14D7DA FOREIGN KEY (offre_de_travail_id) REFERENCES offre_de_travail (id)');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC38B217A7');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('ALTER TABLE commentaire CHANGE description description VARCHAR(5000) NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE competence DROP FOREIGN KEY FK_94D4687F8D0EB82');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687F8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E938DBC740');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9F19F0F9A');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E938DBC740 FOREIGN KEY (candidat_destinataire_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9F19F0F9A FOREIGN KEY (candidat_expediteur_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE education DROP FOREIGN KEY FK_DB0A5ED28D0EB82');
        $this->addSql('ALTER TABLE education CHANGE description description VARCHAR(5000) NOT NULL');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED28D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EFCF77503');
        $this->addSql('ALTER TABLE evenement CHANGE description description VARCHAR(5000) NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE experience_de_travail DROP FOREIGN KEY FK_4330F3848D0EB82');
        $this->addSql('ALTER TABLE experience_de_travail CHANGE description description VARCHAR(5000) NOT NULL');
        $this->addSql('ALTER TABLE experience_de_travail ADD CONSTRAINT FK_4330F3848D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFFCF77503');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C3423929EC4');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C3423929EC4 FOREIGN KEY (candidature_offre_id) REFERENCES candidature_offre (id)');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B338B217A7');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B338B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CFCF77503');
        $this->addSql('ALTER TABLE mission CHANGE nom nom VARCHAR(5000) NOT NULL, CHANGE description description VARCHAR(5000) DEFAULT NULL');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23CFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE offre_de_travail DROP FOREIGN KEY FK_81CF2B1DBCF5E72D');
        $this->addSql('ALTER TABLE offre_de_travail DROP FOREIGN KEY FK_81CF2B1DFCF77503');
        $this->addSql('ALTER TABLE offre_de_travail CHANGE description description VARCHAR(5000) NOT NULL');
        $this->addSql('ALTER TABLE offre_de_travail ADD CONSTRAINT FK_81CF2B1DBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE offre_de_travail ADD CONSTRAINT FK_81CF2B1DFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C67798D0EB82');
        $this->addSql('ALTER TABLE publication CHANGE description description VARCHAR(5000) NOT NULL');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C67798D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EBE6CAE90');
        $this->addSql('ALTER TABLE question CHANGE description description VARCHAR(5000) NOT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id)');
        $this->addSql('ALTER TABLE revue DROP FOREIGN KEY FK_76244F0523929EC4');
        $this->addSql('ALTER TABLE revue ADD CONSTRAINT FK_76244F0523929EC4 FOREIGN KEY (candidature_offre_id) REFERENCES candidature_offre (id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498D0EB82');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FCF77503');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature_mission DROP FOREIGN KEY FK_7ADE8DEABE6CAE90');
        $this->addSql('ALTER TABLE candidature_mission DROP FOREIGN KEY FK_7ADE8DEA8D0EB82');
        $this->addSql('ALTER TABLE candidature_mission ADD CONSTRAINT FK_7ADE8DEABE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidature_mission ADD CONSTRAINT FK_7ADE8DEA8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidature_offre DROP FOREIGN KEY FK_91FCEF3B8D0EB82');
        $this->addSql('ALTER TABLE candidature_offre DROP FOREIGN KEY FK_91FCEF3BF14D7DA');
        $this->addSql('ALTER TABLE candidature_offre ADD CONSTRAINT FK_91FCEF3B8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidature_offre ADD CONSTRAINT FK_91FCEF3BF14D7DA FOREIGN KEY (offre_de_travail_id) REFERENCES offre_de_travail (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX id ON candidature_offre (id)');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC38B217A7');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('ALTER TABLE commentaire CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competence DROP FOREIGN KEY FK_94D4687F8D0EB82');
        $this->addSql('ALTER TABLE competence ADD CONSTRAINT FK_94D4687F8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9F19F0F9A');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E938DBC740');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9F19F0F9A FOREIGN KEY (candidat_expediteur_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E938DBC740 FOREIGN KEY (candidat_destinataire_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE education DROP FOREIGN KEY FK_DB0A5ED28D0EB82');
        $this->addSql('ALTER TABLE education CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED28D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EFCF77503');
        $this->addSql('ALTER TABLE evenement CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE experience_de_travail DROP FOREIGN KEY FK_4330F3848D0EB82');
        $this->addSql('ALTER TABLE experience_de_travail CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE experience_de_travail ADD CONSTRAINT FK_4330F3848D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFFCF77503');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C3423929EC4');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C3423929EC4 FOREIGN KEY (candidature_offre_id) REFERENCES candidature_offre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B338B217A7');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B338B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CFCF77503');
        $this->addSql('ALTER TABLE mission CHANGE nom nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(10383) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23CFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_de_travail DROP FOREIGN KEY FK_81CF2B1DBCF5E72D');
        $this->addSql('ALTER TABLE offre_de_travail DROP FOREIGN KEY FK_81CF2B1DFCF77503');
        $this->addSql('ALTER TABLE offre_de_travail CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE offre_de_travail ADD CONSTRAINT FK_81CF2B1DBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_de_travail ADD CONSTRAINT FK_81CF2B1DFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C67798D0EB82');
        $this->addSql('ALTER TABLE publication CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C67798D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EBE6CAE90');
        $this->addSql('ALTER TABLE question CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE revue DROP FOREIGN KEY FK_76244F0523929EC4');
        $this->addSql('ALTER TABLE revue ADD CONSTRAINT FK_76244F0523929EC4 FOREIGN KEY (candidature_offre_id) REFERENCES candidature_offre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498D0EB82');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FCF77503');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id) ON DELETE CASCADE');
    }
}
