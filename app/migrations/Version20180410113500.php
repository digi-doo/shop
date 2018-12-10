<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180410113500 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order_internal_note DROP FOREIGN KEY FK_971D51758D9F6D38');
        $this->addSql('ALTER TABLE sylius_order_internal_note ADD CONSTRAINT FK_971D51758D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_order_internal_note DROP FOREIGN KEY FK_971D51758D9F6D38');
        $this->addSql('ALTER TABLE sylius_order_internal_note ADD CONSTRAINT FK_971D51758D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
    }
}
