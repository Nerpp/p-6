<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301145738 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image CHANGE trick_id trick_image_id INT NOT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F5876A1D2 FOREIGN KEY (trick_image_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_C53D045F5876A1D2 ON image (trick_image_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F5876A1D2');
        $this->addSql('DROP INDEX IDX_C53D045F5876A1D2 ON image');
        $this->addSql('ALTER TABLE image CHANGE trick_image_id trick_id INT NOT NULL');
    }
}
