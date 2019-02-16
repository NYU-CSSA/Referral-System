<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190216014344 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, resume_id INT NOT NULL, position_id INT NOT NULL, company_id INT NOT NULL, create_date DATETIME NOT NULL, notes VARCHAR(4095) NOT NULL, INDEX IDX_A45BDDC1D262AF09 (resume_id), INDEX IDX_A45BDDC1DD842E46 (position_id), INDEX IDX_A45BDDC1979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE experience (id INT AUTO_INCREMENT NOT NULL, resume_id INT NOT NULL, type VARCHAR(255) NOT NULL, time_period VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, description VARCHAR(4095) NOT NULL, INDEX IDX_590C103D262AF09 (resume_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, desctiption VARCHAR(255) NOT NULL, number INT NOT NULL, INDEX IDX_462CE4F5979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resume (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, name VARCHAR(255) NOT NULL, createtime DATETIME NOT NULL, updatetime DATETIME NOT NULL, grade VARCHAR(255) NOT NULL, gpa DOUBLE PRECISION DEFAULT NULL, major VARCHAR(255) DEFAULT NULL, skills VARCHAR(255) DEFAULT NULL, pdf VARCHAR(255) DEFAULT NULL, intro VARCHAR(1023) DEFAULT NULL, resume_name VARCHAR(255) NOT NULL, INDEX IDX_60C1D0A0CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, gender VARCHAR(45) DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, createtime DATETIME NOT NULL, intro VARCHAR(1023) DEFAULT NULL, UNIQUE INDEX UNIQ_B723AF33E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, createtime DATETIME NOT NULL, UNIQUE INDEX UNIQ_4FBF094FE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_student (company_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_9771B5E4979B1AD6 (company_id), INDEX IDX_9771B5E4CB944F1A (student_id), PRIMARY KEY(company_id, student_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1D262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE experience ADD CONSTRAINT FK_590C103D262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F5979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE resume ADD CONSTRAINT FK_60C1D0A0CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE company_student ADD CONSTRAINT FK_9771B5E4979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_student ADD CONSTRAINT FK_9771B5E4CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1DD842E46');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1D262AF09');
        $this->addSql('ALTER TABLE experience DROP FOREIGN KEY FK_590C103D262AF09');
        $this->addSql('ALTER TABLE resume DROP FOREIGN KEY FK_60C1D0A0CB944F1A');
        $this->addSql('ALTER TABLE company_student DROP FOREIGN KEY FK_9771B5E4CB944F1A');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1979B1AD6');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F5979B1AD6');
        $this->addSql('ALTER TABLE company_student DROP FOREIGN KEY FK_9771B5E4979B1AD6');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE experience');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE resume');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_student');
    }
}
