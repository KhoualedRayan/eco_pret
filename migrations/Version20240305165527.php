<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305165527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce ADD posteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E59DDC44B3 FOREIGN KEY (posteur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F65593E59DDC44B3 ON annonce (posteur_id)');
        $this->addSql('ALTER TABLE annonce_materiel DROP FOREIGN KEY FK_559ABE539DDC44B3');
        $this->addSql('DROP INDEX IDX_559ABE539DDC44B3 ON annonce_materiel');
        $this->addSql('ALTER TABLE annonce_materiel DROP posteur_id');
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B29DDC44B3');
        $this->addSql('DROP INDEX IDX_1BF200B29DDC44B3 ON annonce_service');
        $this->addSql('ALTER TABLE annonce_service DROP posteur_id, DROP titre, DROP description, DROP prix, DROP date_publication, DROP statut, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE annonce_service ADD CONSTRAINT FK_1BF200B2BF396750 FOREIGN KEY (id) REFERENCES annonce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE date_ponctuelle_service ADD CONSTRAINT FK_A63596F34BA556B9 FOREIGN KEY (dateponcts_id) REFERENCES annonce_service (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce_materiel ADD posteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE annonce_materiel ADD CONSTRAINT FK_559ABE539DDC44B3 FOREIGN KEY (posteur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_559ABE539DDC44B3 ON annonce_materiel (posteur_id)');
        $this->addSql('ALTER TABLE date_ponctuelle_service DROP FOREIGN KEY FK_A63596F34BA556B9');
        $this->addSql('ALTER TABLE annonce_service DROP FOREIGN KEY FK_1BF200B2BF396750');
        $this->addSql('ALTER TABLE annonce_service ADD posteur_id INT NOT NULL, ADD titre VARCHAR(255) NOT NULL, ADD description VARCHAR(1023) DEFAULT NULL, ADD prix INT NOT NULL, ADD date_publication DATETIME NOT NULL, ADD statut VARCHAR(127) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE annonce_service ADD CONSTRAINT FK_1BF200B29DDC44B3 FOREIGN KEY (posteur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1BF200B29DDC44B3 ON annonce_service (posteur_id)');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E59DDC44B3');
        $this->addSql('DROP INDEX IDX_F65593E59DDC44B3 ON annonce');
        $this->addSql('ALTER TABLE annonce DROP posteur_id');
    }
}
