<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171117012416 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bitbag_cms_block_translation DROP FOREIGN KEY FK_32897FDF2C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_cms_image DROP FOREIGN KEY FK_743A17B27E3C61F9');
        $this->addSql('ALTER TABLE bitbag_cms_page_products DROP FOREIGN KEY FK_4D64FA85C4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation DROP FOREIGN KEY FK_FDD074A62C2AC5D3');
        $this->addSql('CREATE TABLE bitbag_block (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(64) NOT NULL, type VARCHAR(64) NOT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_F3A318D677153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_block_sections (block_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_2850B691E9ED820C (block_id), INDEX IDX_2850B691D823E37A (section_id), PRIMARY KEY(block_id, section_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_block_products (page_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_B07CAF53C4663E4 (page_id), INDEX IDX_B07CAF534584665A (product_id), PRIMARY KEY(page_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_block_image (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_33AEB0D07E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_block_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, link LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_A0740C9E2C2AC5D3 (translatable_id), UNIQUE INDEX bitbag_block_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_frequently_asked_question (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_frequently_asked_question_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, question VARCHAR(1500) NOT NULL, answer LONGTEXT NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_C0F5E9702C2AC5D3 (translatable_id), UNIQUE INDEX bitbag_frequently_asked_question_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_page (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(250) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C671CD2577153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_page_sections (block_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_A13DB5C4E9ED820C (block_id), INDEX IDX_A13DB5C4D823E37A (section_id), PRIMARY KEY(block_id, section_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_page_products (page_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_3911AC06C4663E4 (page_id), INDEX IDX_3911AC064584665A (product_id), PRIMARY KEY(page_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_page_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, slug VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, meta_keywords VARCHAR(1000) DEFAULT NULL, meta_description VARCHAR(2000) DEFAULT NULL, content LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_D22DAE6A2C2AC5D3 (translatable_id), UNIQUE INDEX bitbag_page_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_section (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(250) NOT NULL, UNIQUE INDEX UNIQ_20A4B05F77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_section_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_D9F264672C2AC5D3 (translatable_id), UNIQUE INDEX bitbag_section_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bitbag_block_sections ADD CONSTRAINT FK_2850B691E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_block (id)');
        $this->addSql('ALTER TABLE bitbag_block_sections ADD CONSTRAINT FK_2850B691D823E37A FOREIGN KEY (section_id) REFERENCES bitbag_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_block_products ADD CONSTRAINT FK_B07CAF53C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_block (id)');
        $this->addSql('ALTER TABLE bitbag_block_products ADD CONSTRAINT FK_B07CAF534584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_block_image ADD CONSTRAINT FK_33AEB0D07E3C61F9 FOREIGN KEY (owner_id) REFERENCES bitbag_block_translation (id)');
        $this->addSql('ALTER TABLE bitbag_block_translation ADD CONSTRAINT FK_A0740C9E2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_frequently_asked_question_translation ADD CONSTRAINT FK_C0F5E9702C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_frequently_asked_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_page_sections ADD CONSTRAINT FK_A13DB5C4E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_page (id)');
        $this->addSql('ALTER TABLE bitbag_page_sections ADD CONSTRAINT FK_A13DB5C4D823E37A FOREIGN KEY (section_id) REFERENCES bitbag_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_page_products ADD CONSTRAINT FK_3911AC06C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_page (id)');
        $this->addSql('ALTER TABLE bitbag_page_products ADD CONSTRAINT FK_3911AC064584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_page_translation ADD CONSTRAINT FK_D22DAE6A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_section_translation ADD CONSTRAINT FK_D9F264672C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_section (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE bitbag_cms_block');
        $this->addSql('DROP TABLE bitbag_cms_block_translation');
        $this->addSql('DROP TABLE bitbag_cms_image');
        $this->addSql('DROP TABLE bitbag_cms_page');
        $this->addSql('DROP TABLE bitbag_cms_page_products');
        $this->addSql('DROP TABLE bitbag_cms_page_translation');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bitbag_block_sections DROP FOREIGN KEY FK_2850B691E9ED820C');
        $this->addSql('ALTER TABLE bitbag_block_products DROP FOREIGN KEY FK_B07CAF53C4663E4');
        $this->addSql('ALTER TABLE bitbag_block_translation DROP FOREIGN KEY FK_A0740C9E2C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_block_image DROP FOREIGN KEY FK_33AEB0D07E3C61F9');
        $this->addSql('ALTER TABLE bitbag_frequently_asked_question_translation DROP FOREIGN KEY FK_C0F5E9702C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_page_sections DROP FOREIGN KEY FK_A13DB5C4E9ED820C');
        $this->addSql('ALTER TABLE bitbag_page_products DROP FOREIGN KEY FK_3911AC06C4663E4');
        $this->addSql('ALTER TABLE bitbag_page_translation DROP FOREIGN KEY FK_D22DAE6A2C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_block_sections DROP FOREIGN KEY FK_2850B691D823E37A');
        $this->addSql('ALTER TABLE bitbag_page_sections DROP FOREIGN KEY FK_A13DB5C4D823E37A');
        $this->addSql('ALTER TABLE bitbag_section_translation DROP FOREIGN KEY FK_D9F264672C2AC5D3');
        $this->addSql('CREATE TABLE bitbag_cms_block (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, type VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_321C84CF77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_block_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, content LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, link LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, locale VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX bitbag_cms_block_translation_uniq_trans (translatable_id, locale), INDEX IDX_32897FDF2C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_image (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, path VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_743A17B27E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_page (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(250) NOT NULL COLLATE utf8_unicode_ci, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_18F07F1B77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_page_products (page_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_4D64FA85C4663E4 (page_id), INDEX IDX_4D64FA854584665A (product_id), PRIMARY KEY(page_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_cms_page_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, slug VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, metaKeywords VARCHAR(1000) DEFAULT NULL COLLATE utf8_unicode_ci, metaDescription VARCHAR(2000) DEFAULT NULL COLLATE utf8_unicode_ci, content LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, locale VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX bitbag_cms_page_translation_uniq_trans (translatable_id, locale), INDEX IDX_FDD074A62C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bitbag_cms_block_translation ADD CONSTRAINT FK_32897FDF2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_image ADD CONSTRAINT FK_743A17B27E3C61F9 FOREIGN KEY (owner_id) REFERENCES bitbag_cms_block_translation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_4D64FA854584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_4D64FA85C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_page (id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation ADD CONSTRAINT FK_FDD074A62C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE bitbag_block');
        $this->addSql('DROP TABLE bitbag_block_sections');
        $this->addSql('DROP TABLE bitbag_block_products');
        $this->addSql('DROP TABLE bitbag_block_image');
        $this->addSql('DROP TABLE bitbag_block_translation');
        $this->addSql('DROP TABLE bitbag_frequently_asked_question');
        $this->addSql('DROP TABLE bitbag_frequently_asked_question_translation');
        $this->addSql('DROP TABLE bitbag_page');
        $this->addSql('DROP TABLE bitbag_page_sections');
        $this->addSql('DROP TABLE bitbag_page_products');
        $this->addSql('DROP TABLE bitbag_page_translation');
        $this->addSql('DROP TABLE bitbag_section');
        $this->addSql('DROP TABLE bitbag_section_translation');
    }
}
