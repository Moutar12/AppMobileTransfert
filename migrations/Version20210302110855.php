<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302110855 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agence ADD admin_agence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agence ADD CONSTRAINT FK_64C19AA93ED2363F FOREIGN KEY (admin_agence_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_64C19AA93ED2363F ON agence (admin_agence_id)');
        $this->addSql('ALTER TABLE compte ADD admin_system_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF6526039622A97 FOREIGN KEY (admin_system_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_CFF6526039622A97 ON compte (admin_system_id)');
        $this->addSql('ALTER TABLE user ADD compte_id INT DEFAULT NULL, ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649F2C56620 ON user (compte_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agence DROP FOREIGN KEY FK_64C19AA93ED2363F');
        $this->addSql('DROP INDEX IDX_64C19AA93ED2363F ON agence');
        $this->addSql('ALTER TABLE agence DROP admin_agence_id');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF6526039622A97');
        $this->addSql('DROP INDEX IDX_CFF6526039622A97 ON compte');
        $this->addSql('ALTER TABLE compte DROP admin_system_id');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649F2C56620');
        $this->addSql('DROP INDEX IDX_8D93D649F2C56620 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP compte_id, DROP type_id');
    }
}
