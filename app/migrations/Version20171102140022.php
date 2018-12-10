<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171102140022 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_variant ADD supplier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product_variant ADD CONSTRAINT FK_A29B5232ADD6D8C FOREIGN KEY (supplier_id) REFERENCES sylius_supplier (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_A29B5232ADD6D8C ON sylius_product_variant (supplier_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_variant DROP FOREIGN KEY FK_A29B5232ADD6D8C');
        $this->addSql('DROP INDEX IDX_A29B5232ADD6D8C ON sylius_product_variant');
        $this->addSql('ALTER TABLE sylius_product_variant DROP supplier_id');
    }
}
