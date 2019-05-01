<?php declare(strict_types = 1);

namespace CoderDojo\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190501180002 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE club_100_payment (id INT AUTO_INCREMENT NOT NULL, donation_id INT DEFAULT NULL, mollie_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, checkout_url VARCHAR(255) NOT NULL, INDEX UNIQ_661185484DC1279C (donation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE club_100_donation (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, payment_id INT DEFAULT NULL, year INT NOT NULL, quarter INT DEFAULT NULL, paid_at DATETIME DEFAULT NULL, uuid VARCHAR(255) NOT NULL, INDEX IDX_37580DBE7597D3FE (member_id), UNIQUE INDEX UNIQ_37580DBE4C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE club_100_payment ADD CONSTRAINT FK_661185484DC1279C FOREIGN KEY (donation_id) REFERENCES club_100_donation (id)');
        $this->addSql('ALTER TABLE club_100_donation ADD CONSTRAINT FK_37580DBE7597D3FE FOREIGN KEY (member_id) REFERENCES club_100 (id)');
        $this->addSql('ALTER TABLE club_100_donation ADD CONSTRAINT FK_37580DBE4C3A3BB FOREIGN KEY (payment_id) REFERENCES club_100_payment (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE club_100_donation DROP FOREIGN KEY FK_37580DBE4C3A3BB');
        $this->addSql('ALTER TABLE club_100_payment DROP FOREIGN KEY FK_661185484DC1279C');
        $this->addSql('DROP TABLE club_100_payment');
        $this->addSql('DROP TABLE club_100_donation');
    }
}
