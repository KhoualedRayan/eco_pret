<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222060338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B2BCF5E72D');
        $this->addSql('DROP INDEX IDX_1BF200B2BCF5E72D ON annonce_service');
        $this->addSql('ALTER TABLE annonce_materiel DROP FOREIGN KEY FK_559ABE53BCF5E72D');
        $this->addSql('DROP INDEX IDX_559ABE53BCF5E72D ON annonce_materiel');
    }
}
