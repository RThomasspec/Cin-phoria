<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240911164336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE installations (id INT AUTO_INCREMENT NOT NULL, salle_id INT NOT NULL, employe_id INT NOT NULL, numero_siege INT DEFAULT NULL, description_probleme LONGTEXT NOT NULL, date_signalement DATE NOT NULL, etat_reparation TINYINT(1) NOT NULL, INDEX IDX_A774F67BDC304035 (salle_id), INDEX IDX_A774F67B1B65292 (employe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE installations ADD CONSTRAINT FK_A774F67BDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE installations ADD CONSTRAINT FK_A774F67B1B65292 FOREIGN KEY (employe_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE installations DROP FOREIGN KEY FK_A774F67BDC304035');
        $this->addSql('ALTER TABLE installations DROP FOREIGN KEY FK_A774F67B1B65292');
        $this->addSql('DROP TABLE installations');
    }
}
