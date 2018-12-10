<?php declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181031221345 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE FULLTEXT INDEX fulltext_taxon_translation_index ON sylius_taxon_translation (name, description)');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_product_code_index ON sylius_product (code)');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_product_name_index ON sylius_product_translation (name)');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_product_description_index ON sylius_product_translation (short_description, description)');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_tag_translation_index ON sylius_tag_translation (name)');
        $this->addSql('CREATE FULLTEXT INDEX fulltext_manufacturer_translation_index ON sylius_manufacturer_translation (name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX fulltext_manufacturer_translation_index ON sylius_manufacturer_translation');
        $this->addSql('DROP INDEX fulltext_product_code_index ON sylius_product');
        $this->addSql('DROP INDEX fulltext_product_name_index ON sylius_product_translation');
        $this->addSql('DROP INDEX fulltext_product_description_index ON sylius_product_translation');
        $this->addSql('DROP INDEX fulltext_tag_translation_index ON sylius_tag_translation');
        $this->addSql('DROP INDEX fulltext_taxon_translation_index ON sylius_taxon_translation');
    }
}
