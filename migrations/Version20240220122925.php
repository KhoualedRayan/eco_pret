<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220122925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_service CHANGE posteur_id posteur_id INT NOT NULL');
        }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B29DDC44B3');
        $this->addSql('DROP INDEX IDX_1BF200B29DDC44B3 ON annonce_service');
        $this->addSql('ALTER TABLE annonce_service CHANGE posteur_id posteur_id INT DEFAULT 5');
        $this->addSql('ALTER TABLE annonce_materiel DROP FOREIGN KEY FK_559ABE539DDC44B3');
        $this->addSql('DROP INDEX IDX_559ABE539DDC44B3 ON annonce_materiel');
    }
}
