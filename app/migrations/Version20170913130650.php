<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170913130650 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_manufacturer (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sylius_manufacturer_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_55F608CC2C2AC5D3 (translatable_id), UNIQUE INDEX sylius_manufacturer_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_manufacturer_translation ADD CONSTRAINT FK_55F608CC2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES sylius_manufacturer (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_manufacturer_translation DROP FOREIGN KEY FK_55F608CC2C2AC5D3');
        $this->addSql('DROP TABLE sylius_manufacturer');
        $this->addSql('DROP TABLE sylius_manufacturer_translation');
    }
}
