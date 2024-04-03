<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402234250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_materiel ADD duree_h INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064046FEB2CF8');
        $this->addSql('DROP INDEX UNIQ_CE6064046FEB2CF8 ON reclamation');
        $this->addSql('ALTER TABLE reclamation DROP annonce_litige_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_materiel DROP duree_h');
        $this->addSql('ALTER TABLE reclamation ADD annonce_litige_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064046FEB2CF8 FOREIGN KEY (annonce_litige_id) REFERENCES annonce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CE6064046FEB2CF8 ON reclamation (annonce_litige_id)');
    }
}
