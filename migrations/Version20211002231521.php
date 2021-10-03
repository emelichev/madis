<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211002231521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_modele_analyse_collectivity (collectivity_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', modele_analyse_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_37CC8294BD56F776 (collectivity_id), INDEX IDX_37CC8294EDA9114E (modele_analyse_id), PRIMARY KEY(collectivity_id, modele_analyse_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aipd_modele_analyse_collectivity ADD CONSTRAINT FK_37CC8294BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE aipd_modele_analyse_collectivity ADD CONSTRAINT FK_37CC8294EDA9114E FOREIGN KEY (modele_analyse_id) REFERENCES aipd_modele (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE aipd_modele_analyse_collectivity');
    }
}
