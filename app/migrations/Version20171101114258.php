<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171101114258 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_default (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, tax_category_id INT DEFAULT NULL, price INT DEFAULT NULL, original_price INT DEFAULT NULL, channel_code VARCHAR(255) NOT NULL, INDEX IDX_18340A514584665A (product_id), INDEX IDX_18340A519DF894ED (tax_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_default ADD CONSTRAINT FK_18340A514584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_default ADD CONSTRAINT FK_18340A519DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE sylius_product_defaults');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_321C84CF77153098 ON bitbag_cms_block (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_18F07F1B77153098 ON bitbag_cms_page (code)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_defaults (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, tax_category_id INT DEFAULT NULL, price INT DEFAULT NULL, original_price INT DEFAULT NULL, channel_code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_77A9A634584665A (product_id), INDEX IDX_77A9A639DF894ED (tax_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_defaults ADD CONSTRAINT FK_77A9A634584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_defaults ADD CONSTRAINT FK_77A9A639DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE sylius_product_default');
        $this->addSql('DROP INDEX UNIQ_321C84CF77153098 ON bitbag_cms_block');
        $this->addSql('DROP INDEX UNIQ_18F07F1B77153098 ON bitbag_cms_page');
    }
}
