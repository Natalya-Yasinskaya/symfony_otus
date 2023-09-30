<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230924154756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email_notification (id BIGSERIAL NOT NULL, email VARCHAR(128) NOT NULL, text VARCHAR(512) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE post DROP title');
        $this->addSql('ALTER TABLE post RENAME COLUMN content TO text');
        $this->addSql('ALTER TABLE "user" ADD password VARCHAR(120) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD age INT NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD is_active BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD token VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone VARCHAR(11) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD email VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD preferred VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD is_protected BOOLEAN DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649AA08CB10 ON "user" (login)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495F37A13B ON "user" (token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE email_notification');
        $this->addSql('ALTER TABLE post ADD title VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE post RENAME COLUMN text TO content');
        $this->addSql('DROP INDEX UNIQ_8D93D649AA08CB10');
        $this->addSql('DROP INDEX UNIQ_8D93D6495F37A13B');
        $this->addSql('ALTER TABLE "user" DROP password');
        $this->addSql('ALTER TABLE "user" DROP age');
        $this->addSql('ALTER TABLE "user" DROP is_active');
        $this->addSql('ALTER TABLE "user" DROP roles');
        $this->addSql('ALTER TABLE "user" DROP token');
        $this->addSql('ALTER TABLE "user" DROP phone');
        $this->addSql('ALTER TABLE "user" DROP email');
        $this->addSql('ALTER TABLE "user" DROP preferred');
        $this->addSql('ALTER TABLE "user" DROP is_protected');
    }
}
