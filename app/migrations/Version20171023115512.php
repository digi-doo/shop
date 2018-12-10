<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171023115512 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_tag DROP FOREIGN KEY FK_44E49A654584665A');
        $this->addSql('ALTER TABLE sylius_product_tag DROP FOREIGN KEY FK_44E49A65BAD26311');
        $this->addSql('ALTER TABLE sylius_product_tag ADD CONSTRAINT FK_44E49A654584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_product_tag ADD CONSTRAINT FK_44E49A65BAD26311 FOREIGN KEY (tag_id) REFERENCES sylius_tag (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product_tag DROP FOREIGN KEY FK_44E49A654584665A');
        $this->addSql('ALTER TABLE sylius_product_tag DROP FOREIGN KEY FK_44E49A65BAD26311');
        $this->addSql('ALTER TABLE sylius_product_tag ADD CONSTRAINT FK_44E49A654584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
        $this->addSql('ALTER TABLE sylius_product_tag ADD CONSTRAINT FK_44E49A65BAD26311 FOREIGN KEY (tag_id) REFERENCES sylius_tag (id)');
    }
}
