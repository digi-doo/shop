<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180221150134 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel_pricing CHANGE discount_percent discount_percent NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product_default CHANGE discount_percent discount_percent NUMERIC(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel_pricing CHANGE discount_percent discount_percent NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE sylius_product_default CHANGE discount_percent discount_percent NUMERIC(10, 2) NOT NULL');
    }
}
