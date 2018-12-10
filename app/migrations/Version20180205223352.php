<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180205223352 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bitbag_shipping_export (id INT AUTO_INCREMENT NOT NULL, shipment_id INT DEFAULT NULL, shipping_gateway_id INT DEFAULT NULL, exported_at DATETIME DEFAULT NULL, label_path VARCHAR(255) DEFAULT NULL, state VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_20E62D9F7BE036FC (shipment_id), INDEX IDX_20E62D9FEF84DE5E (shipping_gateway_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_shipping_gateway (id INT AUTO_INCREMENT NOT NULL, shipping_method_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, config LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', name VARCHAR(255) NOT NULL, INDEX IDX_834E6ECC5F7D6850 (shipping_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bitbag_shipping_export ADD CONSTRAINT FK_20E62D9F7BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id)');
        $this->addSql('ALTER TABLE bitbag_shipping_export ADD CONSTRAINT FK_20E62D9FEF84DE5E FOREIGN KEY (shipping_gateway_id) REFERENCES bitbag_shipping_gateway (id)');
        $this->addSql('ALTER TABLE bitbag_shipping_gateway ADD CONSTRAINT FK_834E6ECC5F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES sylius_shipping_method (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bitbag_shipping_export DROP FOREIGN KEY FK_20E62D9FEF84DE5E');
        $this->addSql('DROP TABLE bitbag_shipping_export');
        $this->addSql('DROP TABLE bitbag_shipping_gateway');
    }
}
