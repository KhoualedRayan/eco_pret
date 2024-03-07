<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307100842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D18805AB2F');
        $this->addSql('DROP INDEX IDX_723705D18805AB2F ON transaction');
        $this->addSql('ALTER TABLE transaction DROP annonce_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recurrence DROP FOREIGN KEY FK_1FB7F221B6FC418C');
        $this->addSql('DROP INDEX IDX_1FB7F221B6FC418C ON recurrence');
        $this->addSql('ALTER TABLE transaction ADD annonce_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_723705D18805AB2F ON transaction (annonce_id)');
    }
}
