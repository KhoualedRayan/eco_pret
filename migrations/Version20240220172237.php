<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220172237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recurrence (id INT AUTO_INCREMENT NOT NULL, type_recurrence VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce_service ADD id_recurrence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE annonce_service ADD CONSTRAINT FK_1BF200B292699D8D FOREIGN KEY (id_recurrence_id) REFERENCES recurrence (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1BF200B292699D8D ON annonce_service (id_recurrence_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B292699D8D');
        $this->addSql('DROP TABLE recurrence');
        $this->addSql('DROP INDEX UNIQ_1BF200B292699D8D ON annonce_service');
        $this->addSql('ALTER TABLE annonce_service DROP id_recurrence_id');
    }
}
