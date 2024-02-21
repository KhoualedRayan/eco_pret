<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221180704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD next_abonnement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B1DF4075 FOREIGN KEY (next_abonnement_id) REFERENCES abonnement (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B1DF4075 ON user (next_abonnement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B1DF4075');
        $this->addSql('DROP INDEX IDX_8D93D649B1DF4075 ON user');
        $this->addSql('ALTER TABLE user DROP next_abonnement_id');
    }
}
