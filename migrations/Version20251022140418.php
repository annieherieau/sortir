<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251022140418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD ending_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP duration, CHANGE starting_date starting_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE register_limit_date register_limit_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD duration TIME NOT NULL, DROP ending_date, CHANGE starting_date starting_date DATETIME NOT NULL, CHANGE register_limit_date register_limit_date DATETIME NOT NULL');
    }
}
