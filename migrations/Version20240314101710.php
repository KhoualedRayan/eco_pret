<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314101710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_reclamation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reclamation ADD tag_reclamation_id INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404B3E1990D FOREIGN KEY (tag_reclamation_id) REFERENCES categorie_reclamation (id)');
        $this->addSql('CREATE INDEX IDX_CE606404B3E1990D ON reclamation (tag_reclamation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404B3E1990D');
        $this->addSql('DROP TABLE categorie_reclamation');
        $this->addSql('DROP INDEX IDX_CE606404B3E1990D ON reclamation');
        $this->addSql('ALTER TABLE reclamation DROP tag_reclamation_id');
    }
}
