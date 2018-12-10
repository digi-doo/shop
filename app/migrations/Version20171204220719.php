<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171204220719 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bitbag_cms_block_sections DROP FOREIGN KEY FK_2850B691E9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections ADD CONSTRAINT FK_5C95115DE9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_products DROP FOREIGN KEY FK_B07CAF53C4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_block_products ADD CONSTRAINT FK_C4B9089FC4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections DROP FOREIGN KEY FK_A13DB5C4E9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections ADD CONSTRAINT FK_D548E347E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_products DROP FOREIGN KEY FK_3911AC06C4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_4D64FA85C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bitbag_cms_block_products DROP FOREIGN KEY FK_C4B9089FC4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_block_products ADD CONSTRAINT FK_B07CAF53C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_block (id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections DROP FOREIGN KEY FK_5C95115DE9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections ADD CONSTRAINT FK_2850B691E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_block (id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_products DROP FOREIGN KEY FK_4D64FA85C4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_3911AC06C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_page (id)');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections DROP FOREIGN KEY FK_D548E347E9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections ADD CONSTRAINT FK_A13DB5C4E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_page (id)');
    }
}
