<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260924070311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, lien_prototype VARCHAR(255) DEFAULT NULL, projet_id INT DEFAULT NULL, responsable_id INT DEFAULT NULL, INDEX IDX_2449BA15C18272 (projet_id), INDEX IDX_2449BA1553C59D72 (responsable_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE hackathon (id INT AUTO_INCREMENT NOT NULL, theme VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, ville VARCHAR(150) NOT NULL, date_heure_debut DATETIME NOT NULL, date_heure_fin DATETIME NOT NULL, affiche VARCHAR(255) DEFAULT NULL, objectifs LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE hackathon_organisateur (hackathon_id INT NOT NULL, organisateur_id INT NOT NULL, INDEX IDX_F4E1E1E9996D90CF (hackathon_id), INDEX IDX_F4E1E1E9D936B2FA (organisateur_id), PRIMARY KEY (hackathon_id, organisateur_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE hackathon_membre_jury (hackathon_id INT NOT NULL, membre_jury_id INT NOT NULL, INDEX IDX_B5FAB720996D90CF (hackathon_id), INDEX IDX_B5FAB72057471281 (membre_jury_id), PRIMARY KEY (hackathon_id, membre_jury_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, competence VARCHAR(255) DEFAULT NULL, hackathon_id INT DEFAULT NULL, participant_id INT NOT NULL, many_to_one_id INT DEFAULT NULL, INDEX IDX_5E90F6D6996D90CF (hackathon_id), INDEX IDX_5E90F6D69D1C3019 (participant_id), INDEX IDX_5E90F6D6EAB5DEB (many_to_one_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE membre (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, type_membre VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE membre_jury (id INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE noter (id INT AUTO_INCREMENT NOT NULL, note DOUBLE PRECISION NOT NULL, membre_jury_id INT NOT NULL, equipe_id INT NOT NULL, INDEX IDX_761C961A57471281 (membre_jury_id), INDEX IDX_761C961A6D861B89 (equipe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, statut VARCHAR(100) NOT NULL, site_web VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE participant (date_naissance DATE NOT NULL, lien_portefolio VARCHAR(255) DEFAULT NULL, id INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, retenu TINYINT NOT NULL, hackathon_id INT NOT NULL, INDEX IDX_50159CA9996D90CF (hackathon_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA15C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA1553C59D72 FOREIGN KEY (responsable_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE hackathon_organisateur ADD CONSTRAINT FK_F4E1E1E9996D90CF FOREIGN KEY (hackathon_id) REFERENCES hackathon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hackathon_organisateur ADD CONSTRAINT FK_F4E1E1E9D936B2FA FOREIGN KEY (organisateur_id) REFERENCES organisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hackathon_membre_jury ADD CONSTRAINT FK_B5FAB720996D90CF FOREIGN KEY (hackathon_id) REFERENCES hackathon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hackathon_membre_jury ADD CONSTRAINT FK_B5FAB72057471281 FOREIGN KEY (membre_jury_id) REFERENCES membre_jury (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6996D90CF FOREIGN KEY (hackathon_id) REFERENCES hackathon (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D69D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6EAB5DEB FOREIGN KEY (many_to_one_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE membre_jury ADD CONSTRAINT FK_53170C13BF396750 FOREIGN KEY (id) REFERENCES membre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE noter ADD CONSTRAINT FK_761C961A57471281 FOREIGN KEY (membre_jury_id) REFERENCES membre_jury (id)');
        $this->addSql('ALTER TABLE noter ADD CONSTRAINT FK_761C961A6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11BF396750 FOREIGN KEY (id) REFERENCES membre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9996D90CF FOREIGN KEY (hackathon_id) REFERENCES hackathon (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15C18272');
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA1553C59D72');
        $this->addSql('ALTER TABLE hackathon_organisateur DROP FOREIGN KEY FK_F4E1E1E9996D90CF');
        $this->addSql('ALTER TABLE hackathon_organisateur DROP FOREIGN KEY FK_F4E1E1E9D936B2FA');
        $this->addSql('ALTER TABLE hackathon_membre_jury DROP FOREIGN KEY FK_B5FAB720996D90CF');
        $this->addSql('ALTER TABLE hackathon_membre_jury DROP FOREIGN KEY FK_B5FAB72057471281');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6996D90CF');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D69D1C3019');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6EAB5DEB');
        $this->addSql('ALTER TABLE membre_jury DROP FOREIGN KEY FK_53170C13BF396750');
        $this->addSql('ALTER TABLE noter DROP FOREIGN KEY FK_761C961A57471281');
        $this->addSql('ALTER TABLE noter DROP FOREIGN KEY FK_761C961A6D861B89');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11BF396750');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9996D90CF');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE hackathon');
        $this->addSql('DROP TABLE hackathon_organisateur');
        $this->addSql('DROP TABLE hackathon_membre_jury');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE membre');
        $this->addSql('DROP TABLE membre_jury');
        $this->addSql('DROP TABLE noter');
        $this->addSql('DROP TABLE organisateur');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE projet');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
