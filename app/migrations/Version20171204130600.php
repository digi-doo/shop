<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171204130600 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
    	$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    	$this->addSql('RENAME TABLE bitbag_block TO bitbag_cms_block');
    	$this->addSql('RENAME TABLE bitbag_block_image TO bitbag_cms_block_image');
    	$this->addSql('RENAME TABLE bitbag_block_translation TO bitbag_cms_block_translation');
    	$this->addSql('RENAME TABLE bitbag_frequently_asked_question TO bitbag_cms_faq');
    	$this->addSql('RENAME TABLE bitbag_frequently_asked_question_translation TO bitbag_cms_faq_translation');
    	$this->addSql('RENAME TABLE bitbag_page TO bitbag_cms_page');
    	$this->addSql('RENAME TABLE bitbag_page_translation TO bitbag_cms_page_translation');
    	$this->addSql('RENAME TABLE bitbag_section TO bitbag_cms_section');
    	$this->addSql('RENAME TABLE bitbag_section_translation TO bitbag_cms_section_translation');
    	
    	$this->addSql('RENAME TABLE bitbag_page_products TO bitbag_cms_page_products');
    	$this->addSql('RENAME TABLE bitbag_page_sections TO bitbag_cms_page_sections');
    	$this->addSql('RENAME TABLE bitbag_block_sections TO bitbag_cms_block_sections');
    	$this->addSql('RENAME TABLE bitbag_block_products TO bitbag_cms_block_products');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    	$this->addSql('RENAME TABLE bitbag_cms_block TO bitbag_block');
    	$this->addSql('RENAME TABLE bitbag_cms_block_image TO bitbag_block_image');
    	$this->addSql('RENAME TABLE bitbag_cms_block_translation TO bitbag_block_translation');
    	$this->addSql('RENAME TABLE bitbag_cms_faq TO bitbag_frequently_asked_question');
    	$this->addSql('RENAME TABLE bitbag_cms_faq_translation TO bitbag_frequently_asked_question_translation');
    	$this->addSql('RENAME TABLE bitbag_cms_page TO bitbag_page');
    	$this->addSql('RENAME TABLE bitbag_cms_page_translation TO bitbag_page_translation');
    	$this->addSql('RENAME TABLE bitbag_cms_section TO bitbag_section');
    	$this->addSql('RENAME TABLE bitbag_cms_section_translation TO bitbag_section_translation');

    	$this->addSql('RENAME TABLE bitbag_cms_page_products TO bitbag_page_products');
    	$this->addSql('RENAME TABLE bitbag_cms_page_sections TO bitbag_page_sections');
    	$this->addSql('RENAME TABLE bitbag_cms_block_sections TO bitbag_block_sections');
    	$this->addSql('RENAME TABLE bitbag_cms_block_products TO bitbag_block_products');
    }
}
