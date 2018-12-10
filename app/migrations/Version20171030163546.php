<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171030163546 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product ADD heureka_id INT DEFAULT NULL, ADD google_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B74448E21E FOREIGN KEY (heureka_id) REFERENCES sylius_heureka_taxonomy (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B7476F5C865 FOREIGN KEY (google_id) REFERENCES sylius_google_taxonomy (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_677B9B74448E21E ON sylius_product (heureka_id)');
        $this->addSql('CREATE INDEX IDX_677B9B7476F5C865 ON sylius_product (google_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B74448E21E');
        $this->addSql('ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B7476F5C865');
        $this->addSql('DROP INDEX IDX_677B9B74448E21E ON sylius_product');
        $this->addSql('DROP INDEX IDX_677B9B7476F5C865 ON sylius_product');
        $this->addSql('ALTER TABLE sylius_product DROP heureka_id, DROP google_id');
    }
}
