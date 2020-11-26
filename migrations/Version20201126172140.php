<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201126172140 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, trick_id INT NOT NULL, comment VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_5F9E962AA76ED395 (user_id), INDEX IDX_5F9E962AB281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, source VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, update_at DATETIME NOT NULL, INDEX IDX_D8F0A91E7A45358C (groupe_id), INDEX IDX_D8F0A91EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick_image (trick_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_E1204E0B281BE2E (trick_id), INDEX IDX_E1204E03DA5256D (image_id), PRIMARY KEY(trick_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick_video (trick_id INT NOT NULL, video_id INT NOT NULL, INDEX IDX_B7E8DA93B281BE2E (trick_id), INDEX IDX_B7E8DA9329C1004E (video_id), PRIMARY KEY(trick_id, video_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6493DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E7A45358C FOREIGN KEY (groupe_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trick_image ADD CONSTRAINT FK_E1204E0B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_image ADD CONSTRAINT FK_E1204E03DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_video ADD CONSTRAINT FK_B7E8DA93B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_video ADD CONSTRAINT FK_B7E8DA9329C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E7A45358C');
        $this->addSql('ALTER TABLE trick_image DROP FOREIGN KEY FK_E1204E03DA5256D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493DA5256D');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AB281BE2E');
        $this->addSql('ALTER TABLE trick_image DROP FOREIGN KEY FK_E1204E0B281BE2E');
        $this->addSql('ALTER TABLE trick_video DROP FOREIGN KEY FK_B7E8DA93B281BE2E');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EA76ED395');
        $this->addSql('ALTER TABLE trick_video DROP FOREIGN KEY FK_B7E8DA9329C1004E');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE trick');
        $this->addSql('DROP TABLE trick_image');
        $this->addSql('DROP TABLE trick_video');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE video');
    }
}
