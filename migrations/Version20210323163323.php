<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210323163323 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ALTER roles TYPE JSON');
        $this->addSql('ALTER TABLE "user" ALTER roles DROP DEFAULT');
        $this->addSql('ALTER TABLE "user" ALTER roles SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".roles IS NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ALTER roles TYPE TEXT');
        $this->addSql('ALTER TABLE "user" ALTER roles DROP DEFAULT');
        $this->addSql('ALTER TABLE "user" ALTER roles DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:simple_array)\'');
    }
}
