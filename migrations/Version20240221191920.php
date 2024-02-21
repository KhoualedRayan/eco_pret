<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221191920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B292699D8D');
        $this->addSql('DROP INDEX UNIQ_1BF200B292699D8D ON annonce_service');
        $this->addSql('ALTER TABLE annonce_service DROP id_recurrence_id, DROP date_fin, CHANGE date_debut date_ponct DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_service ADD id_recurrence_id INT DEFAULT NULL, ADD date_fin DATETIME NOT NULL, CHANGE date_ponct date_debut DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE annonce_service ADD CONSTRAINT FK_1BF200B292699D8D FOREIGN KEY (id_recurrence_id) REFERENCES recurrence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1BF200B292699D8D ON annonce_service (id_recurrence_id)');
    }
}