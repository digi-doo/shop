<?php declare(strict_types=1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181023120010 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_manufacturer_image DROP INDEX IDX_E12799D97E3C61F9, ADD UNIQUE INDEX UNIQ_E12799D97E3C61F9 (owner_id)');
        $this->addSql('ALTER TABLE sylius_manufacturer_image DROP FOREIGN KEY FK_E12799D97E3C61F9');
        $this->addSql('ALTER TABLE sylius_manufacturer_image CHANGE owner_id owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_manufacturer_image ADD CONSTRAINT FK_E12799D97E3C61F9 FOREIGN KEY (owner_id) REFERENCES sylius_manufacturer (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_manufacturer_image DROP INDEX UNIQ_E12799D97E3C61F9, ADD INDEX IDX_E12799D97E3C61F9 (owner_id)');
        $this->addSql('ALTER TABLE sylius_manufacturer_image DROP FOREIGN KEY FK_E12799D97E3C61F9');
        $this->addSql('ALTER TABLE sylius_manufacturer_image CHANGE owner_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_manufacturer_image ADD CONSTRAINT FK_E12799D97E3C61F9 FOREIGN KEY (owner_id) REFERENCES sylius_manufacturer (id) ON DELETE CASCADE');
    }
}
