<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230618105636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE test_id_seq CASCADE');
        $this->addSql('CREATE TABLE post (id BIGSERIAL NOT NULL, author_id BIGINT DEFAULT NULL, title VARCHAR(32) NOT NULL, content VARCHAR(140) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
        $this->addSql('CREATE TABLE subscription (id BIGSERIAL NOT NULL, author_id BIGINT DEFAULT NULL, follower_id BIGINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3C664D3F675F31B ON subscription (author_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3AC24F853 ON subscription (follower_id)');
        $this->addSql('CREATE TABLE "user" (id BIGSERIAL NOT NULL, login VARCHAR(32) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE author_follower (author_id BIGINT NOT NULL, follower_id BIGINT NOT NULL, PRIMARY KEY(author_id, follower_id))');
        $this->addSql('CREATE INDEX IDX_564623F3F675F31B ON author_follower (author_id)');
        $this->addSql('CREATE INDEX IDX_564623F3AC24F853 ON author_follower (follower_id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3AC24F853 FOREIGN KEY (follower_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE author_follower ADD CONSTRAINT FK_564623F3F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE author_follower ADD CONSTRAINT FK_564623F3AC24F853 FOREIGN KEY (follower_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE test');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE test_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE test (id BIGSERIAL NOT NULL, author_id BIGINT DEFAULT NULL)');
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
