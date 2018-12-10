<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180410104001 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_order_internal_note (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, created_by INT NOT NULL, approved_by INT DEFAULT NULL, note LONGTEXT NOT NULL, created_at DATETIME NOT NULL, approved_at DATETIME DEFAULT NULL, INDEX IDX_971D51758D9F6D38 (order_id), INDEX IDX_971D5175DE12AB56 (created_by), INDEX IDX_971D51754EA3CB3D (approved_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_order_internal_note ADD CONSTRAINT FK_971D51758D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE sylius_order_internal_note ADD CONSTRAINT FK_971D5175DE12AB56 FOREIGN KEY (created_by) REFERENCES sylius_admin_user (id)');
        $this->addSql('ALTER TABLE sylius_order_internal_note ADD CONSTRAINT FK_971D51754EA3CB3D FOREIGN KEY (approved_by) REFERENCES sylius_admin_user (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_order_internal_note');
    }
}
