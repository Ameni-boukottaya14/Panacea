<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219215126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordonnance ADD pharmacie_id INT NOT NULL');
        $this->addSql('ALTER TABLE ordonnance ADD CONSTRAINT FK_924B326CBC6D351B FOREIGN KEY (pharmacie_id) REFERENCES pharmacie (id)');
        $this->addSql('CREATE INDEX IDX_924B326CBC6D351B ON ordonnance (pharmacie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordonnance DROP FOREIGN KEY FK_924B326CBC6D351B');
        $this->addSql('DROP INDEX IDX_924B326CBC6D351B ON ordonnance');
        $this->addSql('ALTER TABLE ordonnance DROP pharmacie_id');
    }
}
