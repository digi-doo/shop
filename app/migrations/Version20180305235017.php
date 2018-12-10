<?php declare(strict_types = 1);

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180305235017 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE webburza_wishlist (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, public TINYINT(1) NOT NULL, description TEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_50F062D8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE webburza_wishlist_item (id INT AUTO_INCREMENT NOT NULL, wishlist_id INT NOT NULL, product_variant_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_65EB7091FB8E54CD (wishlist_id), INDEX IDX_65EB7091A80EF684 (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE webburza_wishlist ADD CONSTRAINT FK_50F062D8A76ED395 FOREIGN KEY (user_id) REFERENCES sylius_shop_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE webburza_wishlist_item ADD CONSTRAINT FK_65EB7091FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES webburza_wishlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE webburza_wishlist_item ADD CONSTRAINT FK_65EB7091A80EF684 FOREIGN KEY (product_variant_id) REFERENCES sylius_product_variant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE webburza_wishlist_item DROP FOREIGN KEY FK_65EB7091FB8E54CD');
        $this->addSql('DROP TABLE webburza_wishlist');
        $this->addSql('DROP TABLE webburza_wishlist_item');
    }
}
