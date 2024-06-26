<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312082929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19DDC44B3');
        $this->addSql('DROP INDEX IDX_723705D19DDC44B3 ON transaction');
        $this->addSql('ALTER TABLE transaction ADD prix_final INT DEFAULT NULL, ADD duree_final VARCHAR(255) DEFAULT NULL, DROP posteur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD posteur_id INT NOT NULL, DROP prix_final, DROP duree_final');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19DDC44B3 FOREIGN KEY (posteur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_723705D19DDC44B3 ON transaction (posteur_id)');
    }
}
