<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180729162508 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE maturity_domain (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(80) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maturity (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', domain_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', survey_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', score INT NOT NULL, INDEX IDX_4636E0E8115F0EE5 (domain_id), INDEX IDX_4636E0E8B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maturity_survey (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', score INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E279C54ABD56F776 (collectivity_id), INDEX IDX_E279C54A61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maturity_answer (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', question_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', survey_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', response INT NOT NULL, INDEX IDX_95FB14931E27F6BF (question_id), INDEX IDX_95FB1493B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maturity_question (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', domain_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(80) NOT NULL, INDEX IDX_88BB73A5115F0EE5 (domain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE maturity ADD CONSTRAINT FK_4636E0E8115F0EE5 FOREIGN KEY (domain_id) REFERENCES maturity_domain (id)');
        $this->addSql('ALTER TABLE maturity ADD CONSTRAINT FK_4636E0E8B3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id)');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54ABD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54A61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE maturity_answer ADD CONSTRAINT FK_95FB14931E27F6BF FOREIGN KEY (question_id) REFERENCES maturity_question (id)');
        $this->addSql('ALTER TABLE maturity_answer ADD CONSTRAINT FK_95FB1493B3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id)');
        $this->addSql('ALTER TABLE maturity_question ADD CONSTRAINT FK_88BB73A5115F0EE5 FOREIGN KEY (domain_id) REFERENCES maturity_domain (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity DROP FOREIGN KEY FK_4636E0E8115F0EE5');
        $this->addSql('ALTER TABLE maturity_question DROP FOREIGN KEY FK_88BB73A5115F0EE5');
        $this->addSql('ALTER TABLE maturity DROP FOREIGN KEY FK_4636E0E8B3FE509D');
        $this->addSql('ALTER TABLE maturity_answer DROP FOREIGN KEY FK_95FB1493B3FE509D');
        $this->addSql('ALTER TABLE maturity_answer DROP FOREIGN KEY FK_95FB14931E27F6BF');
        $this->addSql('DROP TABLE maturity_domain');
        $this->addSql('DROP TABLE maturity');
        $this->addSql('DROP TABLE maturity_survey');
        $this->addSql('DROP TABLE maturity_answer');
        $this->addSql('DROP TABLE maturity_question');
    }
}
