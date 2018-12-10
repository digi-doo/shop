<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171204134443 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX uniq_f3a318d677153098 ON bitbag_cms_block');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_321C84CF77153098 ON bitbag_cms_block (code)');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections DROP FOREIGN KEY FK_2850B691D823E37A');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections DROP FOREIGN KEY FK_2850B691E9ED820C');
        $this->addSql('DROP INDEX idx_2850b691e9ed820c ON bitbag_cms_block_sections');
        $this->addSql('CREATE INDEX IDX_5C95115DE9ED820C ON bitbag_cms_block_sections (block_id)');
        $this->addSql('DROP INDEX idx_2850b691d823e37a ON bitbag_cms_block_sections');
        $this->addSql('CREATE INDEX IDX_5C95115DD823E37A ON bitbag_cms_block_sections (section_id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections ADD CONSTRAINT FK_2850B691D823E37A FOREIGN KEY (section_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections ADD CONSTRAINT FK_2850B691E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_block (id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_products DROP FOREIGN KEY FK_B07CAF534584665A');
        $this->addSql('ALTER TABLE bitbag_cms_block_products DROP FOREIGN KEY FK_B07CAF53C4663E4');
        $this->addSql('DROP INDEX idx_b07caf53c4663e4 ON bitbag_cms_block_products');
        $this->addSql('CREATE INDEX IDX_C4B9089FC4663E4 ON bitbag_cms_block_products (page_id)');
        $this->addSql('DROP INDEX idx_b07caf534584665a ON bitbag_cms_block_products');
        $this->addSql('CREATE INDEX IDX_C4B9089F4584665A ON bitbag_cms_block_products (product_id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_products ADD CONSTRAINT FK_B07CAF534584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_products ADD CONSTRAINT FK_B07CAF53C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_block (id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_image DROP FOREIGN KEY FK_33AEB0D07E3C61F9');
        $this->addSql('DROP INDEX uniq_33aeb0d07e3c61f9 ON bitbag_cms_block_image');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D6FD8B297E3C61F9 ON bitbag_cms_block_image (owner_id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_image ADD CONSTRAINT FK_33AEB0D07E3C61F9 FOREIGN KEY (owner_id) REFERENCES bitbag_cms_block_translation (id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_translation DROP FOREIGN KEY FK_A0740C9E2C2AC5D3');
        $this->addSql('DROP INDEX idx_a0740c9e2c2ac5d3 ON bitbag_cms_block_translation');
        $this->addSql('CREATE INDEX IDX_32897FDF2C2AC5D3 ON bitbag_cms_block_translation (translatable_id)');
        $this->addSql('DROP INDEX bitbag_block_translation_uniq_trans ON bitbag_cms_block_translation');
        $this->addSql('CREATE UNIQUE INDEX bitbag_cms_block_translation_uniq_trans ON bitbag_cms_block_translation (translatable_id, locale)');
        $this->addSql('ALTER TABLE bitbag_cms_block_translation ADD CONSTRAINT FK_A0740C9E2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_faq_translation DROP FOREIGN KEY FK_C0F5E9702C2AC5D3');
        $this->addSql('DROP INDEX idx_c0f5e9702c2ac5d3 ON bitbag_cms_faq_translation');
        $this->addSql('CREATE INDEX IDX_8B30DD2E2C2AC5D3 ON bitbag_cms_faq_translation (translatable_id)');
        $this->addSql('DROP INDEX bitbag_frequently_asked_question_translation_uniq_trans ON bitbag_cms_faq_translation');
        $this->addSql('CREATE UNIQUE INDEX bitbag_cms_faq_translation_uniq_trans ON bitbag_cms_faq_translation (translatable_id, locale)');
        $this->addSql('ALTER TABLE bitbag_cms_faq_translation ADD CONSTRAINT FK_C0F5E9702C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_faq (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX uniq_c671cd2577153098 ON bitbag_cms_page');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_18F07F1B77153098 ON bitbag_cms_page (code)');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections DROP FOREIGN KEY FK_A13DB5C4D823E37A');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections DROP FOREIGN KEY FK_A13DB5C4E9ED820C');
        $this->addSql('DROP INDEX idx_a13db5c4e9ed820c ON bitbag_cms_page_sections');
        $this->addSql('CREATE INDEX IDX_D548E347E9ED820C ON bitbag_cms_page_sections (block_id)');
        $this->addSql('DROP INDEX idx_a13db5c4d823e37a ON bitbag_cms_page_sections');
        $this->addSql('CREATE INDEX IDX_D548E347D823E37A ON bitbag_cms_page_sections (section_id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections ADD CONSTRAINT FK_A13DB5C4D823E37A FOREIGN KEY (section_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections ADD CONSTRAINT FK_A13DB5C4E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_page (id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_products DROP FOREIGN KEY FK_3911AC064584665A');
        $this->addSql('ALTER TABLE bitbag_cms_page_products DROP FOREIGN KEY FK_3911AC06C4663E4');
        $this->addSql('DROP INDEX idx_3911ac06c4663e4 ON bitbag_cms_page_products');
        $this->addSql('CREATE INDEX IDX_4D64FA85C4663E4 ON bitbag_cms_page_products (page_id)');
        $this->addSql('DROP INDEX idx_3911ac064584665a ON bitbag_cms_page_products');
        $this->addSql('CREATE INDEX IDX_4D64FA854584665A ON bitbag_cms_page_products (product_id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_3911AC064584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_3911AC06C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_page (id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation DROP FOREIGN KEY FK_D22DAE6A2C2AC5D3');
        $this->addSql('DROP INDEX idx_d22dae6a2c2ac5d3 ON bitbag_cms_page_translation');
        $this->addSql('CREATE INDEX IDX_FDD074A62C2AC5D3 ON bitbag_cms_page_translation (translatable_id)');
        $this->addSql('DROP INDEX bitbag_page_translation_uniq_trans ON bitbag_cms_page_translation');
        $this->addSql('CREATE UNIQUE INDEX bitbag_cms_page_translation_uniq_trans ON bitbag_cms_page_translation (translatable_id, locale)');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation ADD CONSTRAINT FK_D22DAE6A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_section ADD type VARCHAR(250) DEFAULT NULL');
        $this->addSql('DROP INDEX uniq_20a4b05f77153098 ON bitbag_cms_section');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_421D079777153098 ON bitbag_cms_section (code)');
        $this->addSql('ALTER TABLE bitbag_cms_section_translation DROP FOREIGN KEY FK_D9F264672C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_cms_section_translation ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('DROP INDEX idx_d9f264672c2ac5d3 ON bitbag_cms_section_translation');
        $this->addSql('CREATE INDEX IDX_F99CA8582C2AC5D3 ON bitbag_cms_section_translation (translatable_id)');
        $this->addSql('DROP INDEX bitbag_section_translation_uniq_trans ON bitbag_cms_section_translation');
        $this->addSql('CREATE UNIQUE INDEX bitbag_cms_section_translation_uniq_trans ON bitbag_cms_section_translation (translatable_id, locale)');
        $this->addSql('ALTER TABLE bitbag_cms_section_translation ADD CONSTRAINT FK_D9F264672C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX uniq_321c84cf77153098 ON bitbag_cms_block');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F3A318D677153098 ON bitbag_cms_block (code)');
        $this->addSql('ALTER TABLE bitbag_cms_block_image DROP FOREIGN KEY FK_D6FD8B297E3C61F9');
        $this->addSql('DROP INDEX uniq_d6fd8b297e3c61f9 ON bitbag_cms_block_image');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_33AEB0D07E3C61F9 ON bitbag_cms_block_image (owner_id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_image ADD CONSTRAINT FK_D6FD8B297E3C61F9 FOREIGN KEY (owner_id) REFERENCES bitbag_cms_block_translation (id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_products DROP FOREIGN KEY FK_C4B9089FC4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_block_products DROP FOREIGN KEY FK_C4B9089F4584665A');
        $this->addSql('DROP INDEX idx_c4b9089fc4663e4 ON bitbag_cms_block_products');
        $this->addSql('CREATE INDEX IDX_B07CAF53C4663E4 ON bitbag_cms_block_products (page_id)');
        $this->addSql('DROP INDEX idx_c4b9089f4584665a ON bitbag_cms_block_products');
        $this->addSql('CREATE INDEX IDX_B07CAF534584665A ON bitbag_cms_block_products (product_id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_products ADD CONSTRAINT FK_C4B9089FC4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_block (id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_products ADD CONSTRAINT FK_C4B9089F4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections DROP FOREIGN KEY FK_5C95115DE9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections DROP FOREIGN KEY FK_5C95115DD823E37A');
        $this->addSql('DROP INDEX idx_5c95115de9ed820c ON bitbag_cms_block_sections');
        $this->addSql('CREATE INDEX IDX_2850B691E9ED820C ON bitbag_cms_block_sections (block_id)');
        $this->addSql('DROP INDEX idx_5c95115dd823e37a ON bitbag_cms_block_sections');
        $this->addSql('CREATE INDEX IDX_2850B691D823E37A ON bitbag_cms_block_sections (section_id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections ADD CONSTRAINT FK_5C95115DE9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_block (id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections ADD CONSTRAINT FK_5C95115DD823E37A FOREIGN KEY (section_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_translation DROP FOREIGN KEY FK_32897FDF2C2AC5D3');
        $this->addSql('DROP INDEX bitbag_cms_block_translation_uniq_trans ON bitbag_cms_block_translation');
        $this->addSql('CREATE UNIQUE INDEX bitbag_block_translation_uniq_trans ON bitbag_cms_block_translation (translatable_id, locale)');
        $this->addSql('DROP INDEX idx_32897fdf2c2ac5d3 ON bitbag_cms_block_translation');
        $this->addSql('CREATE INDEX IDX_A0740C9E2C2AC5D3 ON bitbag_cms_block_translation (translatable_id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_translation ADD CONSTRAINT FK_32897FDF2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_faq_translation DROP FOREIGN KEY FK_8B30DD2E2C2AC5D3');
        $this->addSql('DROP INDEX bitbag_cms_faq_translation_uniq_trans ON bitbag_cms_faq_translation');
        $this->addSql('CREATE UNIQUE INDEX bitbag_frequently_asked_question_translation_uniq_trans ON bitbag_cms_faq_translation (translatable_id, locale)');
        $this->addSql('DROP INDEX idx_8b30dd2e2c2ac5d3 ON bitbag_cms_faq_translation');
        $this->addSql('CREATE INDEX IDX_C0F5E9702C2AC5D3 ON bitbag_cms_faq_translation (translatable_id)');
        $this->addSql('ALTER TABLE bitbag_cms_faq_translation ADD CONSTRAINT FK_8B30DD2E2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_faq (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX uniq_18f07f1b77153098 ON bitbag_cms_page');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C671CD2577153098 ON bitbag_cms_page (code)');
        $this->addSql('ALTER TABLE bitbag_cms_page_products DROP FOREIGN KEY FK_4D64FA85C4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_page_products DROP FOREIGN KEY FK_4D64FA854584665A');
        $this->addSql('DROP INDEX idx_4d64fa85c4663e4 ON bitbag_cms_page_products');
        $this->addSql('CREATE INDEX IDX_3911AC06C4663E4 ON bitbag_cms_page_products (page_id)');
        $this->addSql('DROP INDEX idx_4d64fa854584665a ON bitbag_cms_page_products');
        $this->addSql('CREATE INDEX IDX_3911AC064584665A ON bitbag_cms_page_products (product_id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_4D64FA85C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_page (id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_4D64FA854584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections DROP FOREIGN KEY FK_D548E347E9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections DROP FOREIGN KEY FK_D548E347D823E37A');
        $this->addSql('DROP INDEX idx_d548e347e9ed820c ON bitbag_cms_page_sections');
        $this->addSql('CREATE INDEX IDX_A13DB5C4E9ED820C ON bitbag_cms_page_sections (block_id)');
        $this->addSql('DROP INDEX idx_d548e347d823e37a ON bitbag_cms_page_sections');
        $this->addSql('CREATE INDEX IDX_A13DB5C4D823E37A ON bitbag_cms_page_sections (section_id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections ADD CONSTRAINT FK_D548E347E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_page (id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections ADD CONSTRAINT FK_D548E347D823E37A FOREIGN KEY (section_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation DROP FOREIGN KEY FK_FDD074A62C2AC5D3');
        $this->addSql('DROP INDEX bitbag_cms_page_translation_uniq_trans ON bitbag_cms_page_translation');
        $this->addSql('CREATE UNIQUE INDEX bitbag_page_translation_uniq_trans ON bitbag_cms_page_translation (translatable_id, locale)');
        $this->addSql('DROP INDEX idx_fdd074a62c2ac5d3 ON bitbag_cms_page_translation');
        $this->addSql('CREATE INDEX IDX_D22DAE6A2C2AC5D3 ON bitbag_cms_page_translation (translatable_id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation ADD CONSTRAINT FK_FDD074A62C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_section DROP type');
        $this->addSql('DROP INDEX uniq_421d079777153098 ON bitbag_cms_section');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_20A4B05F77153098 ON bitbag_cms_section (code)');
        $this->addSql('ALTER TABLE bitbag_cms_section_translation DROP FOREIGN KEY FK_F99CA8582C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_cms_section_translation DROP description');
        $this->addSql('DROP INDEX bitbag_cms_section_translation_uniq_trans ON bitbag_cms_section_translation');
        $this->addSql('CREATE UNIQUE INDEX bitbag_section_translation_uniq_trans ON bitbag_cms_section_translation (translatable_id, locale)');
        $this->addSql('DROP INDEX idx_f99ca8582c2ac5d3 ON bitbag_cms_section_translation');
        $this->addSql('CREATE INDEX IDX_D9F264672C2AC5D3 ON bitbag_cms_section_translation (translatable_id)');
        $this->addSql('ALTER TABLE bitbag_cms_section_translation ADD CONSTRAINT FK_F99CA8582C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
    }
}
