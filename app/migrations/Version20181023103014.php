<?php declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181023103014 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_manufacturer_image (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_E12799D97E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_manufacturer_image ADD CONSTRAINT FK_E12799D97E3C61F9 FOREIGN KEY (owner_id) REFERENCES sylius_manufacturer (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_manufacturer_image');
    }
}
