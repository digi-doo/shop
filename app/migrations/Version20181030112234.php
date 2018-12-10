<?php declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181030112234 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_tag ADD stock_sorting TINYINT(1) DEFAULT NULL COMMENT \'If enabled products will be printed as two groups - in stock and out of stock.\'');
        $this->addSql('ALTER TABLE sylius_manufacturer ADD stock_sorting TINYINT(1) DEFAULT NULL COMMENT \'If enabled products will be printed as two groups - in stock and out of stock.\'');
        $this->addSql('ALTER TABLE sylius_channel ADD search_stock_sorting TINYINT(1) DEFAULT NULL COMMENT \'If enabled searched products will be printed as two groups - in stock and out of stock.\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_channel DROP search_stock_sorting');
        $this->addSql('ALTER TABLE sylius_manufacturer DROP stock_sorting');
        $this->addSql('ALTER TABLE sylius_tag DROP stock_sorting');
    }
}
