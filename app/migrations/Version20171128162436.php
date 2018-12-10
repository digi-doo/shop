<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171128162436 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_payment_method ADD tax_category_id INT DEFAULT NULL, ADD price INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_payment_method ADD CONSTRAINT FK_A75B0B0D9DF894ED FOREIGN KEY (tax_category_id) REFERENCES sylius_tax_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_A75B0B0D9DF894ED ON sylius_payment_method (tax_category_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_payment_method DROP FOREIGN KEY FK_A75B0B0D9DF894ED');
        $this->addSql('DROP INDEX IDX_A75B0B0D9DF894ED ON sylius_payment_method');
        $this->addSql('ALTER TABLE sylius_payment_method DROP tax_category_id, DROP price');
    }
}
