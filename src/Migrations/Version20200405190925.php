<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200405190925 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE family ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE family ADD CONSTRAINT FK_A5E6215BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A5E6215BA76ED395 ON family (user_id)');
        $this->addSql('ALTER TABLE user ADD last_name VARCHAR(50) NOT NULL, ADD first_name VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE family DROP FOREIGN KEY FK_A5E6215BA76ED395');
        $this->addSql('DROP INDEX UNIQ_A5E6215BA76ED395 ON family');
        $this->addSql('ALTER TABLE family DROP user_id');
        $this->addSql('ALTER TABLE user DROP last_name, DROP first_name');
    }
}
