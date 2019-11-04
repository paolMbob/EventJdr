<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191104122020 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, mj_id INT NOT NULL, scenario_id INT NOT NULL, date_debut DATETIME NOT NULL, lieu VARCHAR(255) DEFAULT NULL, date_fin DATETIME DEFAULT NULL, INDEX IDX_D044D5D46A1B4FA4 (mj_id), INDEX IDX_D044D5D4E04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_personnage_joueur (session_id INT NOT NULL, personnage_joueur_id INT NOT NULL, INDEX IDX_F8FBFD7D613FECDF (session_id), INDEX IDX_F8FBFD7D98FB72AC (personnage_joueur_id), PRIMARY KEY(session_id, personnage_joueur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personnage_joueur (id INT AUTO_INCREMENT NOT NULL, race_id INT NOT NULL, user_id INT NOT NULL, point_experience INT DEFAULT NULL, INDEX IDX_3BD7237B6E59D40D (race_id), INDEX IDX_3BD7237BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenario (id INT AUTO_INCREMENT NOT NULL, univers_id INT NOT NULL, nom VARCHAR(255) NOT NULL, texte LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_3E45C8D81CF61C0B (univers_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, auteur_id INT NOT NULL, scenario_id INT NOT NULL, description LONGTEXT NOT NULL, note INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_67F068BC60BB6FE6 (auteur_id), INDEX IDX_67F068BCE04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE univers (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maitre_du_jeu (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_FE38E4E8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_joueur (id INT AUTO_INCREMENT NOT NULL, race VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D46A1B4FA4 FOREIGN KEY (mj_id) REFERENCES maitre_du_jeu (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4E04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE session_personnage_joueur ADD CONSTRAINT FK_F8FBFD7D613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_personnage_joueur ADD CONSTRAINT FK_F8FBFD7D98FB72AC FOREIGN KEY (personnage_joueur_id) REFERENCES personnage_joueur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personnage_joueur ADD CONSTRAINT FK_3BD7237B6E59D40D FOREIGN KEY (race_id) REFERENCES type_joueur (id)');
        $this->addSql('ALTER TABLE personnage_joueur ADD CONSTRAINT FK_3BD7237BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE scenario ADD CONSTRAINT FK_3E45C8D81CF61C0B FOREIGN KEY (univers_id) REFERENCES univers (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id)');
        $this->addSql('ALTER TABLE maitre_du_jeu ADD CONSTRAINT FK_FE38E4E8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE session_personnage_joueur DROP FOREIGN KEY FK_F8FBFD7D613FECDF');
        $this->addSql('ALTER TABLE session_personnage_joueur DROP FOREIGN KEY FK_F8FBFD7D98FB72AC');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4E04E49DF');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCE04E49DF');
        $this->addSql('ALTER TABLE scenario DROP FOREIGN KEY FK_3E45C8D81CF61C0B');
        $this->addSql('ALTER TABLE personnage_joueur DROP FOREIGN KEY FK_3BD7237BA76ED395');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC60BB6FE6');
        $this->addSql('ALTER TABLE maitre_du_jeu DROP FOREIGN KEY FK_FE38E4E8A76ED395');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D46A1B4FA4');
        $this->addSql('ALTER TABLE personnage_joueur DROP FOREIGN KEY FK_3BD7237B6E59D40D');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE session_personnage_joueur');
        $this->addSql('DROP TABLE personnage_joueur');
        $this->addSql('DROP TABLE scenario');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE univers');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE maitre_du_jeu');
        $this->addSql('DROP TABLE type_joueur');
    }
}
