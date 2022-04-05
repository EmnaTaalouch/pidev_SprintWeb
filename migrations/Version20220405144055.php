<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405144055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_user (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_92589AE271F7E88B (event_id), INDEX IDX_92589AE2A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD id_client_id INT DEFAULT NULL, ADD id_responsable_id INT DEFAULT NULL, ADD id_type_id INT DEFAULT NULL, CHANGE demand_status demande_status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA799DED506 FOREIGN KEY (id_client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76EA32074 FOREIGN KEY (id_responsable_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA71BD125E3 FOREIGN KEY (id_type_id) REFERENCES event_type (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA799DED506 ON event (id_client_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA76EA32074 ON event (id_responsable_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA71BD125E3 ON event (id_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA799DED506');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76EA32074');
        $this->addSql('ALTER TABLE event_user DROP FOREIGN KEY FK_92589AE2A76ED395');
        $this->addSql('DROP TABLE event_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA71BD125E3');
        $this->addSql('DROP INDEX IDX_3BAE0AA799DED506 ON event');
        $this->addSql('DROP INDEX IDX_3BAE0AA76EA32074 ON event');
        $this->addSql('DROP INDEX IDX_3BAE0AA71BD125E3 ON event');
        $this->addSql('ALTER TABLE event DROP id_client_id, DROP id_responsable_id, DROP id_type_id, CHANGE demande_status demand_status VARCHAR(255) NOT NULL');
    }
}
