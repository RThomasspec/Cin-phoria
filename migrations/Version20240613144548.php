<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240613144548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5C567F5183');
        $this->addSql('DROP INDEX UNIQ_4E977E5C567F5183 ON salle');
        $this->addSql('ALTER TABLE salle DROP film_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE salle ADD film_id INT NOT NULL');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5C567F5183 FOREIGN KEY (film_id) REFERENCES film (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E977E5C567F5183 ON salle (film_id)');
    }
}
