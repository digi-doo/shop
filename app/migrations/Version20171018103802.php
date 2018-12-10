<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171018103802 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bitbag_cms_block (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(64) NOT NULL, type VARCHAR(64) NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_block_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, link LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_32897FDF2C2AC5D3 (translatable_id), UNIQUE INDEX bitbag_cms_block_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_image (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_743A17B27E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_page (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(250) NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_page_products (page_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_4D64FA85C4663E4 (page_id), INDEX IDX_4D64FA854584665A (product_id), PRIMARY KEY(page_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_page_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, slug VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, metaKeywords VARCHAR(1000) DEFAULT NULL, metaDescription VARCHAR(2000) DEFAULT NULL, content LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_FDD074A62C2AC5D3 (translatable_id), UNIQUE INDEX bitbag_cms_page_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bitbag_cms_block_translation ADD CONSTRAINT FK_32897FDF2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_image ADD CONSTRAINT FK_743A17B27E3C61F9 FOREIGN KEY (owner_id) REFERENCES bitbag_cms_block_translation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_4D64FA85C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_page (id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_4D64FA854584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation ADD CONSTRAINT FK_FDD074A62C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bitbag_cms_block_translation DROP FOREIGN KEY FK_32897FDF2C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_cms_image DROP FOREIGN KEY FK_743A17B27E3C61F9');
        $this->addSql('ALTER TABLE bitbag_cms_page_products DROP FOREIGN KEY FK_4D64FA85C4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation DROP FOREIGN KEY FK_FDD074A62C2AC5D3');
        $this->addSql('DROP TABLE bitbag_cms_block');
        $this->addSql('DROP TABLE bitbag_cms_block_translation');
        $this->addSql('DROP TABLE bitbag_cms_image');
        $this->addSql('DROP TABLE bitbag_cms_page');
        $this->addSql('DROP TABLE bitbag_cms_page_products');
        $this->addSql('DROP TABLE bitbag_cms_page_translation');
    }
}
