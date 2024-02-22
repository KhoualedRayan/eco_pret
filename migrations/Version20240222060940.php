<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222060940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce CHANGE date_publication date_publication DATETIME NOT NULL');
        $this->addSql('ALTER TABLE annonce_materiel CHANGE date_publication date_publication DATETIME NOT NULL');
        $this->addSql('ALTER TABLE annonce_materiel ADD CONSTRAINT FK_559ABE53BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_materiel (id)');
        $this->addSql('CREATE INDEX IDX_559ABE53BCF5E72D ON annonce_materiel (categorie_id)');
        $this->addSql('ALTER TABLE annonce_service CHANGE date_publication date_publication DATETIME NOT NULL');
        $this->addSql('ALTER TABLE annonce_service ADD CONSTRAINT FK_1BF200B2BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_service (id)');
        $this->addSql('CREATE INDEX IDX_1BF200B2BCF5E72D ON annonce_service (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B2BCF5E72D');
        $this->addSql('DROP INDEX IDX_1BF200B2BCF5E72D ON annonce_service');
        $this->addSql('ALTER TABLE annonce_service CHANGE date_publication date_publication DATE NOT NULL');
        $this->addSql('ALTER TABLE annonce_materiel DROP FOREIGN KEY FK_559ABE53BCF5E72D');
        $this->addSql('DROP INDEX IDX_559ABE53BCF5E72D ON annonce_materiel');
        $this->addSql('ALTER TABLE annonce_materiel CHANGE date_publication date_publication DATE NOT NULL');
        $this->addSql('ALTER TABLE annonce CHANGE date_publication date_publication DATE NOT NULL');
    }
}
