<?php declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181030102646 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_taxon ADD stock_sorting TINYINT(1) DEFAULT NULL COMMENT \'If enabled products will be printed as two groups - in stock and out of stock. In each groups will be preserved default position sorting.\', CHANGE filter_enabled filter_enabled TINYINT(1) DEFAULT NULL COMMENT \'Option for showing product filter in shop taxon detail\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_taxon DROP stock_sorting, CHANGE filter_enabled filter_enabled TINYINT(1) DEFAULT NULL');
    }
}
