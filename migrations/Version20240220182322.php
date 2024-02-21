<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220182322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ordonnance (id INT AUTO_INCREMENT NOT NULL, pharmacie_id INT NOT NULL, nom_malade VARCHAR(20) NOT NULL, date DATE NOT NULL, etat VARCHAR(20) NOT NULL, medicaments VARCHAR(255) NOT NULL, prenom_malade VARCHAR(20) NOT NULL, medecin_traitant VARCHAR(20) NOT NULL, INDEX IDX_924B326CBC6D351B (pharmacie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pharmacie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(20) NOT NULL, adress VARCHAR(20) NOT NULL, num_tell INT DEFAULT NULL, adress_email VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ordonnance ADD CONSTRAINT FK_924B326CBC6D351B FOREIGN KEY (pharmacie_id) REFERENCES pharmacie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordonnance DROP FOREIGN KEY FK_924B326CBC6D351B');
        $this->addSql('DROP TABLE ordonnance');
        $this->addSql('DROP TABLE pharmacie');
    }
}
