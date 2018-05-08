<?php declare(strict_types = 1);

namespace CoderDojo\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180504102444 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coc_request ADD expires_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE coc_request ADD expiry_reminder_sent BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE coc_request SET expires_at = DATE_ADD(prepared_at, INTERVAL 30 DAY) WHERE status = \'prepared\'');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coc_request DROP expires_at');
        $this->addSql('ALTER TABLE coc_request DROP expiry_reminder_sent');
    }
}
