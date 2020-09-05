<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200903082116 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trick_pictures (trick_id INT NOT NULL, pictures_id INT NOT NULL, INDEX IDX_EAAFAB7FB281BE2E (trick_id), INDEX IDX_EAAFAB7FBC415685 (pictures_id), PRIMARY KEY(trick_id, pictures_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trick_pictures ADD CONSTRAINT FK_EAAFAB7FB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_pictures ADD CONSTRAINT FK_EAAFAB7FBC415685 FOREIGN KEY (pictures_id) REFERENCES pictures (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC09D86650F');
        $this->addSql('ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC0B46B9EE8');
        $this->addSql('DROP INDEX IDX_8F7C2FC0B46B9EE8 ON pictures');
        $this->addSql('DROP INDEX IDX_8F7C2FC09D86650F ON pictures');
        $this->addSql('ALTER TABLE pictures ADD trick_id INT NOT NULL, DROP user_id_id, DROP trick_id_id, CHANGE path name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_8F7C2FC0B281BE2E ON pictures (trick_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE trick_pictures');
        $this->addSql('ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC0B281BE2E');
        $this->addSql('DROP INDEX IDX_8F7C2FC0B281BE2E ON pictures');
        $this->addSql('ALTER TABLE pictures ADD trick_id_id INT NOT NULL, CHANGE trick_id user_id_id INT NOT NULL, CHANGE name path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC09D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0B46B9EE8 FOREIGN KEY (trick_id_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_8F7C2FC0B46B9EE8 ON pictures (trick_id_id)');
        $this->addSql('CREATE INDEX IDX_8F7C2FC09D86650F ON pictures (user_id_id)');
    }
}
