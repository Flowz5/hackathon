<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261119071315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Inscription : clé primaire composée (id_hackathon, num)';
    }

    public function up(Schema $schema): void
    {
        // Données de test uniquement (rechargées par les fixtures) : les lignes existantes
        // ne peuvent pas recevoir de valeur valide pour la nouvelle clé (id_hackathon, num).
        $this->addSql('DELETE FROM inscription');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY `FK_5E90F6D6996D90CF`');
        $this->addSql('DROP INDEX IDX_5E90F6D6996D90CF ON inscription');
        $this->addSql('ALTER TABLE inscription MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE inscription ADD num INT NOT NULL, ADD id_hackathon INT NOT NULL, DROP id, DROP hackathon_id, DROP PRIMARY KEY, ADD PRIMARY KEY (id_hackathon, num)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6B0F4A68 FOREIGN KEY (id_hackathon) REFERENCES hackathon (id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D6B0F4A68 ON inscription (id_hackathon)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6B0F4A68');
        $this->addSql('DROP INDEX IDX_5E90F6D6B0F4A68 ON inscription');
        $this->addSql('ALTER TABLE inscription ADD id INT AUTO_INCREMENT NOT NULL, ADD hackathon_id INT DEFAULT NULL, DROP num, DROP id_hackathon, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT `FK_5E90F6D6996D90CF` FOREIGN KEY (hackathon_id) REFERENCES hackathon (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5E90F6D6996D90CF ON inscription (hackathon_id)');
    }
}
