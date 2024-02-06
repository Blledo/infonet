<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206155227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE characters (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, mass VARCHAR(10) DEFAULT NULL, height VARCHAR(10) DEFAULT NULL, gender VARCHAR(10) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE movies_characters (character_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_6BDFABF8C70F0E28 (character_id), INDEX IDX_6BDFABF88F93B6FC (movie_id), PRIMARY KEY(character_id, movie_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE movies (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE movies_characters ADD CONSTRAINT FK_6BDFABF8C70F0E28 FOREIGN KEY (character_id) REFERENCES characters (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movies_characters ADD CONSTRAINT FK_6BDFABF88F93B6FC FOREIGN KEY (movie_id) REFERENCES movies (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movies_characters DROP FOREIGN KEY FK_6BDFABF8C70F0E28');
        $this->addSql('ALTER TABLE movies_characters DROP FOREIGN KEY FK_6BDFABF88F93B6FC');
        $this->addSql('DROP TABLE characters');
        $this->addSql('DROP TABLE movies_characters');
        $this->addSql('DROP TABLE movies');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
