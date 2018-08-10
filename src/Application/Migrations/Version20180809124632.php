<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180809124632 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_request (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', object VARCHAR(255) DEFAULT NULL, other_object VARCHAR(255) DEFAULT NULL, date DATE NOT NULL, reason VARCHAR(255) NOT NULL, complete TINYINT(1) NOT NULL, legitimate_applicant TINYINT(1) NOT NULL, legitimate_request TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', applicant_civility VARCHAR(255) DEFAULT NULL, applicant_first_name VARCHAR(255) DEFAULT NULL, applicant_last_name VARCHAR(255) DEFAULT NULL, applicant_address VARCHAR(255) DEFAULT NULL, applicant_mail VARCHAR(255) DEFAULT NULL, applicant_phone_number VARCHAR(10) DEFAULT NULL, applicant_concerned_people TINYINT(1) NOT NULL, concerned_civility VARCHAR(255) DEFAULT NULL, concerned_first_name VARCHAR(255) DEFAULT NULL, concerned_last_name VARCHAR(255) DEFAULT NULL, concerned_address VARCHAR(255) DEFAULT NULL, concerned_mail VARCHAR(255) DEFAULT NULL, concerned_phone_number VARCHAR(10) DEFAULT NULL, concerned_link_with_applicant VARCHAR(255) DEFAULT NULL, answer_response VARCHAR(255) DEFAULT NULL, answer_date DATE DEFAULT NULL, answer_type VARCHAR(255) DEFAULT NULL, INDEX IDX_3CDC30CDBD56F776 (collectivity_id), INDEX IDX_3CDC30CD61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_request ADD CONSTRAINT FK_3CDC30CDBD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_request ADD CONSTRAINT FK_3CDC30CD61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE registry_request');
    }
}
