<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220409023424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE relationnel DROP FOREIGN KEY relationnel_ibfk_1');
        $this->addSql('ALTER TABLE offres DROP FOREIGN KEY offres_ibfk_1');
        $this->addSql('ALTER TABLE comptabilite DROP FOREIGN KEY comptabilite_ibfk_1');
        $this->addSql('CREATE TABLE event_user (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_92589AE271F7E88B (event_id), INDEX IDX_92589AE2A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE comptabilite');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE offres');
        $this->addSql('DROP TABLE prest_part');
        $this->addSql('DROP TABLE relationnel');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE saisons');
        $this->addSql('DROP TABLE type_comptabilite');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY fk_clientsec');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY fk_event_type');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY fk_responsable');
        $this->addSql('DROP INDEX fk_clientsec ON event');
        $this->addSql('DROP INDEX fk_event_type ON event');
        $this->addSql('DROP INDEX fk_responsable ON event');
        $this->addSql('ALTER TABLE event ADD id_client_id INT DEFAULT NULL, ADD id_responsable_id INT DEFAULT NULL, ADD id_type_id INT DEFAULT NULL, ADD demande_status VARCHAR(255) NOT NULL, ADD nbr_participants INT NOT NULL, ADD lieu VARCHAR(255) NOT NULL, ADD image_event VARCHAR(255) NOT NULL, DROP id_client, DROP id_responsable, DROP id_type');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA799DED506 FOREIGN KEY (id_client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76EA32074 FOREIGN KEY (id_responsable_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA71BD125E3 FOREIGN KEY (id_type_id) REFERENCES event_type (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA799DED506 ON event (id_client_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA76EA32074 ON event (id_responsable_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA71BD125E3 ON event (id_type_id)');
        $this->addSql('DROP INDEX fk_rec ON reclamation');
        $this->addSql('ALTER TABLE reclamation CHANGE id_client user_id INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CE606404A76ED395 ON reclamation (user_id)');
        $this->addSql('DROP INDEX login_pk ON user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE comptabilite (id INT AUTO_INCREMENT NOT NULL, id_type INT NOT NULL, libelle VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, description VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, date DATE NOT NULL, INDEX id_type (id_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, id_client INT NOT NULL, id_evenement INT NOT NULL, commentaire VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, rate DOUBLE PRECISION NOT NULL, date_evaluation DATE NOT NULL, INDEX fk_rec (id_client), INDEX id_evenement (id_evenement), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE offres (id INT NOT NULL, saison INT NOT NULL, nom VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, INDEX saison (saison), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE prest_part (id INT AUTO_INCREMENT NOT NULL, id_resp_pres_part INT NOT NULL, nom VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, num_fiscal VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, type VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, source VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, date DATE NOT NULL, description VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, INDEX fk_pres_part (id_resp_pres_part), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE `latin1_swedish_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE relationnel (id INT AUTO_INCREMENT NOT NULL, id_categorie INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, image VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, rating DOUBLE PRECISION NOT NULL, INDEX id_categorie (id_categorie), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, id_reclamation INT NOT NULL, text TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, INDEX id_reclamation (id_reclamation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE saisons (id INT NOT NULL, nom VARCHAR(20) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE type_comptabilite (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, montant DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE comptabilite ADD CONSTRAINT comptabilite_ibfk_1 FOREIGN KEY (id_type) REFERENCES type_comptabilite (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT evaluation_ibfk_1 FOREIGN KEY (id_evenement) REFERENCES event (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT fk_rec FOREIGN KEY (id_client) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offres ADD CONSTRAINT offres_ibfk_1 FOREIGN KEY (saison) REFERENCES saisons (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE prest_part ADD CONSTRAINT fk_pres_part FOREIGN KEY (id_resp_pres_part) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE relationnel ADD CONSTRAINT relationnel_ibfk_1 FOREIGN KEY (id_categorie) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT reponse_ibfk_1 FOREIGN KEY (id_reclamation) REFERENCES reclamation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE event_user');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA799DED506');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76EA32074');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA71BD125E3');
        $this->addSql('DROP INDEX IDX_3BAE0AA799DED506 ON event');
        $this->addSql('DROP INDEX IDX_3BAE0AA76EA32074 ON event');
        $this->addSql('DROP INDEX IDX_3BAE0AA71BD125E3 ON event');
        $this->addSql('ALTER TABLE event ADD id_responsable INT NOT NULL, ADD id_type INT NOT NULL, DROP id_client_id, DROP id_responsable_id, DROP id_type_id, DROP demande_status, DROP lieu, DROP image_event, CHANGE nbr_participants id_client INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT fk_clientsec FOREIGN KEY (id_client) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT fk_event_type FOREIGN KEY (id_type) REFERENCES event_type (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT fk_responsable FOREIGN KEY (id_responsable) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX fk_clientsec ON event (id_client)');
        $this->addSql('CREATE INDEX fk_event_type ON event (id_type)');
        $this->addSql('CREATE INDEX fk_responsable ON event (id_responsable)');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('DROP INDEX IDX_CE606404A76ED395 ON reclamation');
        $this->addSql('ALTER TABLE reclamation CHANGE user_id id_client INT NOT NULL');
        $this->addSql('CREATE INDEX fk_rec ON reclamation (id_client)');
        $this->addSql('CREATE UNIQUE INDEX login_pk ON user (login)');
    }
}
