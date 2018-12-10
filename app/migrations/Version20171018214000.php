<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171018214000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_product_tag (product_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_44E49A654584665A (product_id), INDEX IDX_44E49A65BAD26311 (tag_id), PRIMARY KEY(product_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_tag (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_tag_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_82132EB1989D9B62 (slug), INDEX IDX_82132EB12C2AC5D3 (translatable_id), UNIQUE INDEX sylius_tag_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_product_tag ADD CONSTRAINT FK_44E49A654584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
        $this->addSql('ALTER TABLE sylius_product_tag ADD CONSTRAINT FK_44E49A65BAD26311 FOREIGN KEY (tag_id) REFERENCES sylius_tag (id)');
        $this->addSql('ALTER TABLE sylius_tag_translation ADD CONSTRAINT FK_82132EB12C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_tag (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_tag DROP FOREIGN KEY FK_44E49A65BAD26311');
        $this->addSql('ALTER TABLE sylius_tag_translation DROP FOREIGN KEY FK_82132EB12C2AC5D3');
        $this->addSql('DROP TABLE sylius_product_tag');
        $this->addSql('DROP TABLE sylius_tag');
        $this->addSql('DROP TABLE sylius_tag_translation');
    }
}
