<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200916184252 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trick_videos (trick_id INT NOT NULL, videos_id INT NOT NULL, INDEX IDX_72BFE52FB281BE2E (trick_id), INDEX IDX_72BFE52F763C10B2 (videos_id), PRIMARY KEY(trick_id, videos_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trick_videos ADD CONSTRAINT FK_72BFE52FB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_videos ADD CONSTRAINT FK_72BFE52F763C10B2 FOREIGN KEY (videos_id) REFERENCES videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432B46B9EE8');
        $this->addSql('DROP INDEX IDX_29AA6432B46B9EE8 ON videos');
        $this->addSql('ALTER TABLE videos DROP trick_id_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE trick_videos');
        $this->addSql('ALTER TABLE videos ADD trick_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432B46B9EE8 FOREIGN KEY (trick_id_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_29AA6432B46B9EE8 ON videos (trick_id_id)');
    }
}
