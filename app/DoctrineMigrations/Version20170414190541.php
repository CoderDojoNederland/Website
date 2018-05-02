<?php declare(strict_types = 1);

namespace CoderDojo\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170414190541 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE coc_request (id VARCHAR(255) NOT NULL, user_id INT DEFAULT NULL, dojo_id INT DEFAULT NULL, letters VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, notes LONGTEXT DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, prepared_at DATETIME DEFAULT NULL, requested_at DATETIME DEFAULT NULL, received_at DATETIME DEFAULT NULL, INDEX IDX_CD4E12C2A76ED395 (user_id), INDEX IDX_CD4E12C232F09E9C (dojo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Claim (id INT AUTO_INCREMENT NOT NULL, dojo_id INT DEFAULT NULL, user_id INT DEFAULT NULL, hash VARCHAR(255) NOT NULL, expires DATETIME NOT NULL, claimed DATETIME DEFAULT NULL, INDEX IDX_66A8F12332F09E9C (dojo_id), INDEX IDX_66A8F123A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE DojoRequest (id INT AUTO_INCREMENT NOT NULL, dojo_id INT DEFAULT NULL, user_id INT DEFAULT NULL, requested DATETIME NOT NULL, approved DATETIME DEFAULT NULL, INDEX IDX_843A542C32F09E9C (dojo_id), INDEX IDX_843A542CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE DojoEvent (id INT AUTO_INCREMENT NOT NULL, dojo_id INT DEFAULT NULL, zen_id VARCHAR(255) DEFAULT NULL, event_type VARCHAR(255) DEFAULT \'custom\' NOT NULL, name VARCHAR(255) NOT NULL, dojodate DATE NOT NULL, url VARCHAR(255) NOT NULL, eventbrite_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_86958828CB55F2F1 (eventbrite_id), INDEX IDX_8695882832F09E9C (dojo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Dojo (id INT AUTO_INCREMENT NOT NULL, zen_id VARCHAR(255) DEFAULT NULL, zen_creator_email VARCHAR(255) DEFAULT NULL, zen_url VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, lat NUMERIC(9, 6) NOT NULL, lon NUMERIC(9, 6) NOT NULL, email VARCHAR(255) NOT NULL, website VARCHAR(255) NOT NULL, twitter VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_dojos (dojo_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_408B7C9432F09E9C (dojo_id), INDEX IDX_408B7C94A76ED395 (user_id), PRIMARY KEY(dojo_id, user_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coc_request ADD CONSTRAINT FK_CD4E12C2A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE coc_request ADD CONSTRAINT FK_CD4E12C232F09E9C FOREIGN KEY (dojo_id) REFERENCES Dojo (id)');
        $this->addSql('ALTER TABLE Claim ADD CONSTRAINT FK_66A8F12332F09E9C FOREIGN KEY (dojo_id) REFERENCES Dojo (id)');
        $this->addSql('ALTER TABLE Claim ADD CONSTRAINT FK_66A8F123A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE DojoRequest ADD CONSTRAINT FK_843A542C32F09E9C FOREIGN KEY (dojo_id) REFERENCES Dojo (id)');
        $this->addSql('ALTER TABLE DojoRequest ADD CONSTRAINT FK_843A542CA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE DojoEvent ADD CONSTRAINT FK_8695882832F09E9C FOREIGN KEY (dojo_id) REFERENCES Dojo (id)');
        $this->addSql('ALTER TABLE users_dojos ADD CONSTRAINT FK_408B7C9432F09E9C FOREIGN KEY (dojo_id) REFERENCES Dojo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_dojos ADD CONSTRAINT FK_408B7C94A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6612469DE2');
        $this->addSql('ALTER TABLE coc_request DROP FOREIGN KEY FK_CD4E12C2A76ED395');
        $this->addSql('ALTER TABLE Claim DROP FOREIGN KEY FK_66A8F123A76ED395');
        $this->addSql('ALTER TABLE DojoRequest DROP FOREIGN KEY FK_843A542CA76ED395');
        $this->addSql('ALTER TABLE users_dojos DROP FOREIGN KEY FK_408B7C94A76ED395');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B');
        $this->addSql('ALTER TABLE coc_request DROP FOREIGN KEY FK_CD4E12C232F09E9C');
        $this->addSql('ALTER TABLE Claim DROP FOREIGN KEY FK_66A8F12332F09E9C');
        $this->addSql('ALTER TABLE DojoRequest DROP FOREIGN KEY FK_843A542C32F09E9C');
        $this->addSql('ALTER TABLE DojoEvent DROP FOREIGN KEY FK_8695882832F09E9C');
        $this->addSql('ALTER TABLE users_dojos DROP FOREIGN KEY FK_408B7C9432F09E9C');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE coc_request');
        $this->addSql('DROP TABLE Claim');
        $this->addSql('DROP TABLE DojoRequest');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE DojoEvent');
        $this->addSql('DROP TABLE Dojo');
        $this->addSql('DROP TABLE users_dojos');
        $this->addSql('DROP TABLE article');
    }
}
