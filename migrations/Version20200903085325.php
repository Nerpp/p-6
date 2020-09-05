<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200903085325 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick_pictures DROP FOREIGN KEY FK_EAAFAB7FBC415685');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6AB281BE2E (trick_id), UNIQUE INDEX UNIQ_E01FBE6AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE pictures');
        $this->addSql('DROP TABLE trick_pictures');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pictures (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_8F7C2FC0B281BE2E (trick_id), UNIQUE INDEX UNIQ_8F7C2FC0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE trick_pictures (trick_id INT NOT NULL, pictures_id INT NOT NULL, INDEX IDX_EAAFAB7FB281BE2E (trick_id), INDEX IDX_EAAFAB7FBC415685 (pictures_id), PRIMARY KEY(trick_id, pictures_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE trick_pictures ADD CONSTRAINT FK_EAAFAB7FB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_pictures ADD CONSTRAINT FK_EAAFAB7FBC415685 FOREIGN KEY (pictures_id) REFERENCES pictures (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE images');
    }
}
