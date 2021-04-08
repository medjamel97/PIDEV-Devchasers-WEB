<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210407223610 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCFB88E14F');
        $this->addSql('CREATE TABLE relation (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP INDEX IDX_67F068BCFB88E14F ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP utilisateur_id');
        $this->addSql('ALTER TABLE user ADD candidat_id INT DEFAULT NULL, ADD societe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6498D0EB82 ON user (candidat_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649FCF77503 ON user (societe_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, societe_id INT DEFAULT NULL, candidat_id INT DEFAULT NULL, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mot_de_passe VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type_utilisateur INT NOT NULL, UNIQUE INDEX UNIQ_1D1C63B38D0EB82 (candidat_id), UNIQUE INDEX UNIQ_1D1C63B3FCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B38D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3FCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('DROP TABLE relation');
        $this->addSql('ALTER TABLE commentaire ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_67F068BCFB88E14F ON commentaire (utilisateur_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498D0EB82');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FCF77503');
        $this->addSql('DROP INDEX UNIQ_8D93D6498D0EB82 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649FCF77503 ON user');
        $this->addSql('ALTER TABLE user DROP candidat_id, DROP societe_id');
    }
}
