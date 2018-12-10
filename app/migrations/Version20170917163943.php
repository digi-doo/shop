<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170917163943 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_taxon ADD enabled TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_product_variant ADD code1 VARCHAR(255) DEFAULT NULL, ADD code2 VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_taxon DROP enabled');
        $this->addSql('ALTER TABLE sylius_product_variant DROP code1, DROP code2');
    }
}
