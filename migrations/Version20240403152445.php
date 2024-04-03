<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403152445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404B3E1990D');
        $this->addSql('DROP TABLE categorie_reclamation');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064046FEB2CF8');
        $this->addSql('DROP INDEX UNIQ_CE6064046FEB2CF8 ON reclamation');
        $this->addSql('DROP INDEX IDX_CE606404B3E1990D ON reclamation');
        $this->addSql('ALTER TABLE reclamation DROP tag_reclamation_id, DROP annonce_litige_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_reclamation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reclamation ADD tag_reclamation_id INT NOT NULL, ADD annonce_litige_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404B3E1990D FOREIGN KEY (tag_reclamation_id) REFERENCES categorie_reclamation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064046FEB2CF8 FOREIGN KEY (annonce_litige_id) REFERENCES annonce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CE6064046FEB2CF8 ON reclamation (annonce_litige_id)');
        $this->addSql('CREATE INDEX IDX_CE606404B3E1990D ON reclamation (tag_reclamation_id)');
    }
}
