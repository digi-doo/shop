<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171101135623 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B742ADD6D8C');
        $this->addSql('DROP INDEX IDX_677B9B742ADD6D8C ON sylius_product');
        $this->addSql('ALTER TABLE sylius_product DROP supplier_id');
        $this->addSql('ALTER TABLE sylius_product_default ADD supplier_id INT DEFAULT NULL, ADD enabled_mass_original_price TINYINT(1) NOT NULL, ADD enabled_mass_tax_category TINYINT(1) NOT NULL, ADD enabled_mass_supplier TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE sylius_product_default ADD CONSTRAINT FK_18340A512ADD6D8C FOREIGN KEY (supplier_id) REFERENCES sylius_supplier (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_18340A512ADD6D8C ON sylius_product_default (supplier_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product ADD supplier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B742ADD6D8C FOREIGN KEY (supplier_id) REFERENCES sylius_supplier (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_677B9B742ADD6D8C ON sylius_product (supplier_id)');
        $this->addSql('ALTER TABLE sylius_product_default DROP FOREIGN KEY FK_18340A512ADD6D8C');
        $this->addSql('DROP INDEX IDX_18340A512ADD6D8C ON sylius_product_default');
        $this->addSql('ALTER TABLE sylius_product_default DROP supplier_id, DROP enabled_mass_original_price, DROP enabled_mass_tax_category, DROP enabled_mass_supplier');
    }
}
