<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307095153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recurrence ADD CONSTRAINT FK_1FB7F221B6FC418C FOREIGN KEY (annonce_serv_id) REFERENCES annonce_service (id)');
        $this->addSql('CREATE INDEX IDX_1FB7F221B6FC418C ON recurrence (annonce_serv_id)');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19DDC44B3');
        $this->addSql('DROP INDEX IDX_723705D19DDC44B3 ON transaction');
        $this->addSql('ALTER TABLE transaction DROP posteur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recurrence DROP FOREIGN KEY FK_1FB7F221B6FC418C');
        $this->addSql('DROP INDEX IDX_1FB7F221B6FC418C ON recurrence');
        $this->addSql('ALTER TABLE transaction ADD posteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19DDC44B3 FOREIGN KEY (posteur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_723705D19DDC44B3 ON transaction (posteur_id)');
    }
}
