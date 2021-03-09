<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210228010559 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD code_transaction VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4CF2C56620');
        $this->addSql('DROP INDEX IDX_EAA81A4CF2C56620 ON transactions');
        $this->addSql('ALTER TABLE transactions ADD compte_retrait_id INT DEFAULT NULL, ADD type VARCHAR(255) NOT NULL, CHANGE compte_id compte_envoie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C81D20A4F FOREIGN KEY (compte_envoie_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4CB6EC9AC4 FOREIGN KEY (compte_retrait_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4C81D20A4F ON transactions (compte_envoie_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4CB6EC9AC4 ON transactions (compte_retrait_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP code_transaction');
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4C81D20A4F');
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4CB6EC9AC4');
        $this->addSql('DROP INDEX IDX_EAA81A4C81D20A4F ON transactions');
        $this->addSql('DROP INDEX IDX_EAA81A4CB6EC9AC4 ON transactions');
        $this->addSql('ALTER TABLE transactions ADD compte_id INT DEFAULT NULL, DROP compte_envoie_id, DROP compte_retrait_id, DROP type');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4CF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4CF2C56620 ON transactions (compte_id)');
    }
}
