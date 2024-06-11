<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607000015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, payment_amount INT NOT NULL, payment_methode VARCHAR(255) NOT NULL, payment_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_orders (product_id INT NOT NULL, orders_id INT NOT NULL, INDEX IDX_8753BC4A4584665A (product_id), INDEX IDX_8753BC4ACFFE9AD6 (orders_id), PRIMARY KEY(product_id, orders_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_orders ADD CONSTRAINT FK_8753BC4A4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_orders ADD CONSTRAINT FK_8753BC4ACFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_orders DROP FOREIGN KEY FK_8753BC4A4584665A');
        $this->addSql('ALTER TABLE product_orders DROP FOREIGN KEY FK_8753BC4ACFFE9AD6');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE product_orders');
    }
}
