<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170916145301 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_shipping_method_payments (shipping_method_id INT NOT NULL, payment_method_id INT NOT NULL, INDEX IDX_BB5E4AB15F7D6850 (shipping_method_id), INDEX IDX_BB5E4AB15AA1164F (payment_method_id), PRIMARY KEY(shipping_method_id, payment_method_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sylius_shipping_method_payments ADD CONSTRAINT FK_BB5E4AB15F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES sylius_shipping_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_shipping_method_payments ADD CONSTRAINT FK_BB5E4AB15AA1164F FOREIGN KEY (payment_method_id) REFERENCES sylius_payment_method (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_channel ADD stock_emails VARCHAR(255) DEFAULT NULL, ADD bank_account VARCHAR(255) DEFAULT NULL, ADD support_number VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_shipping_method_payments');
        $this->addSql('ALTER TABLE sylius_channel DROP stock_emails, DROP bank_account, DROP support_number');
    }
}
