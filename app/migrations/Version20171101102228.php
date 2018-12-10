<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171101102228 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_defaults (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, tax_category_id INT DEFAULT NULL, price INT DEFAULT NULL, original_price INT DEFAULT NULL, channel_code VARCHAR(255) NOT NULL, INDEX IDX_77A9A634584665A (product_id), INDEX IDX_77A9A639DF894ED (tax_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_defaults ADD CONSTRAINT FK_77A9A634584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_defaults ADD CONSTRAINT FK_77A9A639DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE sylius_product_channel_pricing');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_channel_pricing (id INT AUTO_INCREMENT NOT NULL, tax_category_id INT DEFAULT NULL, product_id INT NOT NULL, price INT DEFAULT NULL, original_price INT DEFAULT NULL, channel_code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_26F816E49DF894ED (tax_category_id), INDEX IDX_26F816E44584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_channel_pricing ADD CONSTRAINT FK_26F816E44584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_channel_pricing ADD CONSTRAINT FK_26F816E49DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE sylius_product_defaults');
    }
}
