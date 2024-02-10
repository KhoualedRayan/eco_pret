<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240210173812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_materiel ADD titre VARCHAR(255) NOT NULL, ADD description VARCHAR(1023) DEFAULT NULL, ADD prix INT NOT NULL, ADD date_publication DATE NOT NULL, ADD statut VARCHAR(127) NOT NULL');
        $this->addSql('ALTER TABLE annonce_service ADD titre VARCHAR(255) NOT NULL, ADD description VARCHAR(1023) DEFAULT NULL, ADD prix INT NOT NULL, ADD date_publication DATE NOT NULL, ADD statut VARCHAR(127) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_service DROP titre, DROP description, DROP prix, DROP date_publication, DROP statut');
        $this->addSql('ALTER TABLE annonce_materiel DROP titre, DROP description, DROP prix, DROP date_publication, DROP statut');
    }
}
