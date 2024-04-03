<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403155740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, administrateur_id INT DEFAULT NULL, transaction_id INT DEFAULT NULL, objet VARCHAR(255) NOT NULL, description VARCHAR(2047) NOT NULL, statut_reclamation VARCHAR(127) NOT NULL, titre VARCHAR(255) NOT NULL, date_poste DATE NOT NULL, INDEX IDX_CE606404A76ED395 (user_id), INDEX IDX_CE6064047EE5403C (administrateur_id), INDEX IDX_CE6064042FC0CB0F (transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064047EE5403C FOREIGN KEY (administrateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064042FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064047EE5403C');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064042FC0CB0F');
        $this->addSql('DROP TABLE reclamation');
    }
}
