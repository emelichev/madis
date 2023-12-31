<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210610144913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE registry_treatment SET collecting_method = CONCAT(\'["\', collecting_method, \'"]\')');
        $this->addSql('ALTER TABLE registry_treatment ADD coordonnees_responsable_traitement VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment CHANGE collecting_method collecting_method JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment DROP coordonnees_responsable_traitement');
        $this->addSql('ALTER TABLE registry_treatment CHANGE collecting_method collecting_method VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
