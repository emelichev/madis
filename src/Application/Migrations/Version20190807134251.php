<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190807134251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_proof_treatment (proof_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', treatment_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_313C16DDD7086615 (proof_id), INDEX IDX_313C16DD471C0366 (treatment_id), PRIMARY KEY(proof_id, treatment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_proof_contractor (proof_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', contractor_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_EA6E4196D7086615 (proof_id), INDEX IDX_EA6E4196B0265DC7 (contractor_id), PRIMARY KEY(proof_id, contractor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_proof_treatment ADD CONSTRAINT FK_313C16DDD7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id)');
        $this->addSql('ALTER TABLE registry_proof_treatment ADD CONSTRAINT FK_313C16DD471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id)');
        $this->addSql('ALTER TABLE registry_proof_contractor ADD CONSTRAINT FK_EA6E4196D7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id)');
        $this->addSql('ALTER TABLE registry_proof_contractor ADD CONSTRAINT FK_EA6E4196B0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE registry_proof_treatment');
        $this->addSql('DROP TABLE registry_proof_contractor');
    }
}
