<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190308124038 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cached_element ADD name VARCHAR(255) NOT NULL, ADD date_created DATETIME DEFAULT NULL, ADD service_id INT NOT NULL, DROP date_create, CHANGE date_update date_update DATETIME DEFAULT NULL, CHANGE docker_id docker_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cached_element ADD docker_id VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD date_create DATETIME NOT NULL, DROP docker_name, DROP name, DROP date_created, DROP service_id, CHANGE date_update date_update DATETIME NOT NULL');
    }
}
