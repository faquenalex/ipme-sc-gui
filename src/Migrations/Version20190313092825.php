<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190313092825 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cached_element_metadata (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, cached_element_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cached_element DROP FOREIGN KEY FK_21D266DA89697FA8');
        $this->addSql('DROP INDEX IDX_21D266DA89697FA8 ON cached_element');
        $this->addSql('ALTER TABLE cached_element ADD service_id INT NOT NULL, DROP serviceId');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE cached_element_metadata');
        $this->addSql('ALTER TABLE cached_element ADD serviceId INT DEFAULT NULL, DROP service_id');
        $this->addSql('ALTER TABLE cached_element ADD CONSTRAINT FK_21D266DA89697FA8 FOREIGN KEY (serviceId) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_21D266DA89697FA8 ON cached_element (serviceId)');
    }
}
