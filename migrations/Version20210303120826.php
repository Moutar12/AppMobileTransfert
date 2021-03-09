<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210303120826 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCD725330D');
        $this->addSql('DROP INDEX IDX_47948BBCD725330D ON depot');
        $this->addSql('ALTER TABLE depot DROP agence_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depot ADD agence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCD725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('CREATE INDEX IDX_47948BBCD725330D ON depot (agence_id)');
    }
}
