<?php declare(strict_types = 1);

namespace CoderDojo\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20230422145121 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE club_100 DROP COLUMN hash, DROP COLUMN payment_interval, DROP COLUMN confirmation_url');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0;');
        $this->addSql('DROP TABLE club_100_payment');
        $this->addSql('DROP TABLE club_100_donation');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1;');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Dojo DROP verified_at');
    }
}
