<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200405123544 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE family_event (family_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_7D999D4C35E566A (family_id), INDEX IDX_7D999D471F7E88B (event_id), PRIMARY KEY(family_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE family_event ADD CONSTRAINT FK_7D999D4C35E566A FOREIGN KEY (family_id) REFERENCES family (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE family_event ADD CONSTRAINT FK_7D999D471F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE family_event');
    }
}
