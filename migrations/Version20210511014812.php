<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511014812 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C67798D0EB82');
        $this->addSql('DROP INDEX IDX_AF3C67798D0EB82 ON publication');
        $this->addSql('ALTER TABLE publication CHANGE candidat_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AF3C6779A76ED395 ON publication (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779A76ED395');
        $this->addSql('DROP INDEX IDX_AF3C6779A76ED395 ON publication');
        $this->addSql('ALTER TABLE publication CHANGE user_id candidat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C67798D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('CREATE INDEX IDX_AF3C67798D0EB82 ON publication (candidat_id)');
    }
}
