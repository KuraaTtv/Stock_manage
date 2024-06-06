<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606093038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items ADD prod_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0F91A0F34 FOREIGN KEY (prod_id_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_62809DB0F91A0F34 ON order_items (prod_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB0F91A0F34');
        $this->addSql('DROP INDEX IDX_62809DB0F91A0F34 ON order_items');
        $this->addSql('ALTER TABLE order_items DROP prod_id_id');
    }
}
