<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211101902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E59DDC44B3');
        $this->addSql('DROP INDEX IDX_F65593E59DDC44B3 ON annonce');
        $this->addSql('ALTER TABLE annonce DROP posteur_id');
        $this->addSql('ALTER TABLE annonce_materiel ADD posteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE annonce_materiel ADD CONSTRAINT FK_559ABE539DDC44B3 FOREIGN KEY (posteur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_559ABE539DDC44B3 ON annonce_materiel (posteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_materiel DROP FOREIGN KEY FK_559ABE539DDC44B3');
        $this->addSql('DROP INDEX IDX_559ABE539DDC44B3 ON annonce_materiel');
        $this->addSql('ALTER TABLE annonce_materiel DROP posteur_id');
        $this->addSql('ALTER TABLE annonce ADD posteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E59DDC44B3 FOREIGN KEY (posteur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F65593E59DDC44B3 ON annonce (posteur_id)');
    }
}
