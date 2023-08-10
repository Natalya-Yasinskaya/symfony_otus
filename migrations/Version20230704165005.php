<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704165005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD password VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD age INT NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD location VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER updated_at SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP password');
        $this->addSql('ALTER TABLE "user" DROP age');
        $this->addSql('ALTER TABLE "user" DROP location');
        $this->addSql('ALTER TABLE "user" ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER updated_at DROP NOT NULL');
    }
}
