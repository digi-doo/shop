<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180210222615 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_variant ADD external_code VARCHAR(255) DEFAULT NULL, ADD negative_stock TINYINT(1) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A29B523E20A50B4 ON sylius_product_variant (external_code)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_A29B523E20A50B4 ON sylius_product_variant');
        $this->addSql('ALTER TABLE sylius_product_variant DROP external_code, DROP negative_stock');
    }
}
