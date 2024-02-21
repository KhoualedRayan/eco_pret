<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221200944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE date_ponctuelle_service (id INT AUTO_INCREMENT NOT NULL, dateponcts_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_A63596F34BA556B9 (dateponcts_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE date_ponctuelle_service ADD CONSTRAINT FK_A63596F34BA556B9 FOREIGN KEY (dateponcts_id) REFERENCES annonce_service (id)');
        $this->addSql('ALTER TABLE recurrence ADD date_debut DATETIME NOT NULL, ADD date_fin DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE date_ponctuelle_service DROP FOREIGN KEY FK_A63596F34BA556B9');
        $this->addSql('DROP TABLE date_ponctuelle_service');
        $this->addSql('ALTER TABLE recurrence DROP date_debut, DROP date_fin');
    }
}
