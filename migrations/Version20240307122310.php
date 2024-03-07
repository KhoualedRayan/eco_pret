<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307122310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file_attente (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, annonce_id INT NOT NULL, rang INT NOT NULL, INDEX IDX_4F10E0F2A76ED395 (user_id), INDEX IDX_4F10E0F28805AB2F (annonce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file_attente ADD CONSTRAINT FK_4F10E0F2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file_attente ADD CONSTRAINT FK_4F10E0F28805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE annonce_user DROP FOREIGN KEY FK_B7E60AD7A76ED395');
        $this->addSql('ALTER TABLE annonce_user DROP FOREIGN KEY FK_B7E60AD78805AB2F');
        $this->addSql('DROP TABLE annonce_user');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E52FC0CB0F');
        $this->addSql('DROP INDEX UNIQ_F65593E52FC0CB0F ON annonce');
        $this->addSql('ALTER TABLE annonce DROP transaction_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce_user (annonce_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B7E60AD78805AB2F (annonce_id), INDEX IDX_B7E60AD7A76ED395 (user_id), PRIMARY KEY(annonce_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE annonce_user ADD CONSTRAINT FK_B7E60AD7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE annonce_user ADD CONSTRAINT FK_B7E60AD78805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file_attente DROP FOREIGN KEY FK_4F10E0F2A76ED395');
        $this->addSql('ALTER TABLE file_attente DROP FOREIGN KEY FK_4F10E0F28805AB2F');
        $this->addSql('DROP TABLE file_attente');
        $this->addSql('ALTER TABLE annonce ADD transaction_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E52FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F65593E52FC0CB0F ON annonce (transaction_id)');
    }
}
