<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181123003815 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE resume (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, name VARCHAR(255) NOT NULL, createtime DATETIME NOT NULL, updatetime DATETIME NOT NULL, INDEX IDX_60C1D0A0CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, gender VARCHAR(45) DEFAULT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, createtime DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_resume (company_id INT NOT NULL, resume_id INT NOT NULL, INDEX IDX_5D5EB4D5979B1AD6 (company_id), INDEX IDX_5D5EB4D5D262AF09 (resume_id), PRIMARY KEY(company_id, resume_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_student (company_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_9771B5E4979B1AD6 (company_id), INDEX IDX_9771B5E4CB944F1A (student_id), PRIMARY KEY(company_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resume ADD CONSTRAINT FK_60C1D0A0CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE company_resume ADD CONSTRAINT FK_5D5EB4D5979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_resume ADD CONSTRAINT FK_5D5EB4D5D262AF09 FOREIGN KEY (resume_id) REFERENCES resume (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_student ADD CONSTRAINT FK_9771B5E4979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company_student ADD CONSTRAINT FK_9771B5E4CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company_resume DROP FOREIGN KEY FK_5D5EB4D5D262AF09');
        $this->addSql('ALTER TABLE resume DROP FOREIGN KEY FK_60C1D0A0CB944F1A');
        $this->addSql('ALTER TABLE company_student DROP FOREIGN KEY FK_9771B5E4CB944F1A');
        $this->addSql('ALTER TABLE company_resume DROP FOREIGN KEY FK_5D5EB4D5979B1AD6');
        $this->addSql('ALTER TABLE company_student DROP FOREIGN KEY FK_9771B5E4979B1AD6');
        $this->addSql('DROP TABLE resume');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_resume');
        $this->addSql('DROP TABLE company_student');
    }
}
