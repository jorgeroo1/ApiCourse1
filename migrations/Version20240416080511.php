<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240416080511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE fortune_cookie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, icon_key VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE fortune_cookie (id INT NOT NULL, category_id INT NOT NULL, fortune VARCHAR(255) NOT NULL, number_printed INT NOT NULL, discontinued BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F8D8B48712469DE2 ON fortune_cookie (category_id)');
        $this->addSql('COMMENT ON COLUMN fortune_cookie.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE fortune_cookie ADD CONSTRAINT FK_F8D8B48712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE fortune_cookie_id_seq CASCADE');
        $this->addSql('ALTER TABLE fortune_cookie DROP CONSTRAINT FK_F8D8B48712469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE fortune_cookie');
    }
}
