<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170916110048 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product ADD supplier_id INT DEFAULT NULL, ADD manufacturer_id INT DEFAULT NULL, ADD code1 VARCHAR(255) DEFAULT NULL, ADD code2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B742ADD6D8C FOREIGN KEY (supplier_id) REFERENCES sylius_supplier (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B74A23B42D FOREIGN KEY (manufacturer_id) REFERENCES sylius_manufacturer (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_677B9B742ADD6D8C ON sylius_product (supplier_id)');
        $this->addSql('CREATE INDEX IDX_677B9B74A23B42D ON sylius_product (manufacturer_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B742ADD6D8C');
        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B74A23B42D');
        $this->addSql('DROP INDEX IDX_677B9B742ADD6D8C ON sylius_product');
        $this->addSql('DROP INDEX IDX_677B9B74A23B42D ON sylius_product');
        $this->addSql('ALTER TABLE sylius_product DROP supplier_id, DROP manufacturer_id, DROP code1, DROP code2');
    }
}
