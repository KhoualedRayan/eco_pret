<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240210214117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_materiel DROP FOREIGN KEY FK_559ABE53BA29911A');
        $this->addSql('DROP INDEX UNIQ_559ABE53BA29911A ON annonce_materiel');
        $this->addSql('ALTER TABLE annonce_materiel DROP posteur_id_id');
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B2BA29911A');
        $this->addSql('DROP INDEX UNIQ_1BF200B2BA29911A ON annonce_service');
        $this->addSql('ALTER TABLE annonce_service DROP posteur_id_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_service ADD posteur_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE annonce_service ADD CONSTRAINT FK_1BF200B2BA29911A FOREIGN KEY (posteur_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1BF200B2BA29911A ON annonce_service (posteur_id_id)');
        $this->addSql('ALTER TABLE annonce_materiel ADD posteur_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE annonce_materiel ADD CONSTRAINT FK_559ABE53BA29911A FOREIGN KEY (posteur_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_559ABE53BA29911A ON annonce_materiel (posteur_id_id)');
    }
}
