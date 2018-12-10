<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180225224234 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bitbag_cms_block ADD tag_id INT DEFAULT NULL, ADD taxon_id INT DEFAULT NULL, ADD tab_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bitbag_cms_block ADD CONSTRAINT FK_321C84CFBAD26311 FOREIGN KEY (tag_id) REFERENCES sylius_tag (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bitbag_cms_block ADD CONSTRAINT FK_321C84CFDE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_321C84CFBAD26311 ON bitbag_cms_block (tag_id)');
        $this->addSql('CREATE INDEX IDX_321C84CFDE13F470 ON bitbag_cms_block (taxon_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bitbag_cms_block DROP FOREIGN KEY FK_321C84CFBAD26311');
        $this->addSql('ALTER TABLE bitbag_cms_block DROP FOREIGN KEY FK_321C84CFDE13F470');
        $this->addSql('DROP INDEX IDX_321C84CFBAD26311 ON bitbag_cms_block');
        $this->addSql('DROP INDEX IDX_321C84CFDE13F470 ON bitbag_cms_block');
        $this->addSql('ALTER TABLE bitbag_cms_block DROP tag_id, DROP taxon_id, DROP tab_type');
    }
}
