<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221028100705 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment ADD exempt_AIPD TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE collectivity_id collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE cloned_from_id cloned_from_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE service_id service_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE software software VARCHAR(255) DEFAULT NULL, CHANGE legal_basis legal_basis JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE delay_number delay_number INT DEFAULT NULL, CHANGE delay_period delay_period VARCHAR(255) DEFAULT NULL, CHANGE manager manager VARCHAR(255) DEFAULT NULL, CHANGE security_access_control_comment security_access_control_comment VARCHAR(255) DEFAULT NULL, CHANGE security_tracability_comment security_tracability_comment VARCHAR(255) DEFAULT NULL, CHANGE security_saving_comment security_saving_comment VARCHAR(255) DEFAULT NULL, CHANGE security_update_comment security_update_comment VARCHAR(255) DEFAULT NULL, CHANGE security_other_comment security_other_comment VARCHAR(255) DEFAULT NULL, CHANGE template_identifier template_identifier INT DEFAULT NULL, CHANGE data_origin data_origin VARCHAR(255) DEFAULT NULL, CHANGE author author VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_particular_comment concerned_people_particular_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_user_comment concerned_people_user_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_agent_comment concerned_people_agent_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_elected_comment concerned_people_elected_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_company_comment concerned_people_company_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_partner_comment concerned_people_partner_comment VARCHAR(255) DEFAULT NULL, CHANGE concerned_people_other_comment concerned_people_other_comment VARCHAR(255) DEFAULT NULL, CHANGE collecting_method collecting_method JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', CHANGE estimated_concerned_people estimated_concerned_people INT DEFAULT NULL, CHANGE ultimate_fate ultimate_fate VARCHAR(255) DEFAULT NULL, CHANGE coordonnees_responsable_traitement coordonnees_responsable_traitement VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment DROP exempt_AIPD, CHANGE cloned_from_id cloned_from_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE service_id service_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE collectivity_id collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE manager manager VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE software software VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE legal_basis legal_basis JSON CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', CHANGE data_origin data_origin VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE template_identifier template_identifier INT DEFAULT NULL, CHANGE author author VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE coordonnees_responsable_traitement coordonnees_responsable_traitement VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE collecting_method collecting_method JSON CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', CHANGE estimated_concerned_people estimated_concerned_people INT DEFAULT NULL, CHANGE ultimate_fate ultimate_fate VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE delay_number delay_number INT DEFAULT NULL, CHANGE delay_period delay_period VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE security_access_control_comment security_access_control_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE security_tracability_comment security_tracability_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE security_saving_comment security_saving_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE security_update_comment security_update_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE security_other_comment security_other_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_particular_comment concerned_people_particular_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_user_comment concerned_people_user_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_agent_comment concerned_people_agent_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_elected_comment concerned_people_elected_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_company_comment concerned_people_company_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_partner_comment concerned_people_partner_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE concerned_people_other_comment concerned_people_other_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
