<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240713064818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depot ADD prix_achat DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE dette DROP prix_achat');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depot DROP prix_achat');
        $this->addSql('ALTER TABLE dette ADD prix_achat DOUBLE PRECISION DEFAULT NULL');
    }
}
