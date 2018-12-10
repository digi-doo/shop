<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180224181620 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_tag CHANGE enabled enabled TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE sylius_manufacturer CHANGE enabled enabled TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE sylius_supplier CHANGE enabled enabled TINYINT(1) DEFAULT \'1\' NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_manufacturer CHANGE enabled enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE sylius_supplier CHANGE enabled enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE sylius_tag CHANGE enabled enabled TINYINT(1) NOT NULL');
    }
}
