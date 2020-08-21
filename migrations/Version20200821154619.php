<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200821154619 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, trick_id_id INT NOT NULL, comment VARCHAR(255) NOT NULL, datetime DATETIME NOT NULL, INDEX IDX_5F9E962A9D86650F (user_id_id), INDEX IDX_5F9E962AB46B9EE8 (trick_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pic (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, trick_id_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_CB34514E9D86650F (user_id_id), INDEX IDX_CB34514EB46B9EE8 (trick_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick (id INT AUTO_INCREMENT NOT NULL, trickgroupid_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, create_date DATETIME NOT NULL, updatedate DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D8F0A91E1D1C69B8 (trickgroupid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE videos (id INT AUTO_INCREMENT NOT NULL, trick_id_id INT NOT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_29AA6432B46B9EE8 (trick_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AB46B9EE8 FOREIGN KEY (trick_id_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE pic ADD CONSTRAINT FK_CB34514E9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pic ADD CONSTRAINT FK_CB34514EB46B9EE8 FOREIGN KEY (trick_id_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E1D1C69B8 FOREIGN KEY (trickgroupid_id) REFERENCES trick_group (id)');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432B46B9EE8 FOREIGN KEY (trick_id_id) REFERENCES trick (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AB46B9EE8');
        $this->addSql('ALTER TABLE pic DROP FOREIGN KEY FK_CB34514EB46B9EE8');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432B46B9EE8');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E1D1C69B8');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A9D86650F');
        $this->addSql('ALTER TABLE pic DROP FOREIGN KEY FK_CB34514E9D86650F');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE pic');
        $this->addSql('DROP TABLE trick');
        $this->addSql('DROP TABLE trick_group');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE videos');
    }
}
