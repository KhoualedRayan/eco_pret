<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307102228 extends AbstractMigration
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
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19DDC44B3');
        $this->addSql('DROP INDEX IDX_723705D19DDC44B3 ON transaction');
        $this->addSql('ALTER TABLE transaction DROP posteur_id');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E52FC0CB0F');
        $this->addSql('DROP INDEX UNIQ_F65593E52FC0CB0F ON annonce');
        $this->addSql('ALTER TABLE annonce DROP transaction_id');
    }
}
