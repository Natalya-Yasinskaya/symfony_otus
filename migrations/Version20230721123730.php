<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230721123730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post (id BIGSERIAL NOT NULL, author_id BIGINT DEFAULT NULL, title VARCHAR(32) NOT NULL, content VARCHAR(140) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX post__author_id__ind ON post (author_id)');
        $this->addSql('CREATE TABLE subscription (id BIGSERIAL NOT NULL, author_id BIGINT DEFAULT NULL, follower_id BIGINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX subscription__author_id__ind ON subscription (author_id)');
        $this->addSql('CREATE INDEX subscription__follower_id__ind ON subscription (follower_id)');
        $this->addSql('CREATE TABLE "user" (id BIGSERIAL NOT NULL, login VARCHAR(32) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token VARCHAR(32) DEFAULT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495F37A13B ON "user" (token)');
        $this->addSql('CREATE TABLE author_follower (author_id BIGINT NOT NULL, follower_id BIGINT NOT NULL, PRIMARY KEY(author_id, follower_id))');
        $this->addSql('CREATE INDEX IDX_564623F3F675F31B ON author_follower (author_id)');
        $this->addSql('CREATE INDEX IDX_564623F3AC24F853 ON author_follower (follower_id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3AC24F853 FOREIGN KEY (follower_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE author_follower ADD CONSTRAINT FK_564623F3F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE author_follower ADD CONSTRAINT FK_564623F3AC24F853 FOREIGN KEY (follower_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3F675F31B');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3AC24F853');
        $this->addSql('ALTER TABLE author_follower DROP CONSTRAINT FK_564623F3F675F31B');
        $this->addSql('ALTER TABLE author_follower DROP CONSTRAINT FK_564623F3AC24F853');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE author_follower');
    }
}
