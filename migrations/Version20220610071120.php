<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220610071120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favoritos (recurso_id INT NOT NULL, usuario_id INT NOT NULL, INDEX IDX_1E86887FE52B6C4E (recurso_id), INDEX IDX_1E86887FDB38439E (usuario_id), PRIMARY KEY(recurso_id, usuario_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acceso (recurso_id INT NOT NULL, usuario_id INT NOT NULL, INDEX IDX_1268771BE52B6C4E (recurso_id), INDEX IDX_1268771BDB38439E (usuario_id), PRIMARY KEY(recurso_id, usuario_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favoritos ADD CONSTRAINT FK_1E86887FE52B6C4E FOREIGN KEY (recurso_id) REFERENCES recurso (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favoritos ADD CONSTRAINT FK_1E86887FDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acceso ADD CONSTRAINT FK_1268771BE52B6C4E FOREIGN KEY (recurso_id) REFERENCES recurso (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acceso ADD CONSTRAINT FK_1268771BDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE audio ADD recurso_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audio ADD CONSTRAINT FK_187D3695E52B6C4E FOREIGN KEY (recurso_id) REFERENCES recurso (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_187D3695E52B6C4E ON audio (recurso_id)');
        $this->addSql('ALTER TABLE imagen ADD recurso_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE imagen ADD CONSTRAINT FK_8319D2B3E52B6C4E FOREIGN KEY (recurso_id) REFERENCES recurso (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8319D2B3E52B6C4E ON imagen (recurso_id)');
        $this->addSql('ALTER TABLE modelo3d ADD recurso_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE modelo3d ADD CONSTRAINT FK_B78F45FE52B6C4E FOREIGN KEY (recurso_id) REFERENCES recurso (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B78F45FE52B6C4E ON modelo3d (recurso_id)');
        $this->addSql('ALTER TABLE recurso ADD propietario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recurso ADD CONSTRAINT FK_B2BB376453C8D32C FOREIGN KEY (propietario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_B2BB376453C8D32C ON recurso (propietario_id)');
        $this->addSql('ALTER TABLE usuario ADD tier_id INT NOT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05DA354F9DC FOREIGN KEY (tier_id) REFERENCES tier (id)');
        $this->addSql('CREATE INDEX IDX_2265B05DA354F9DC ON usuario (tier_id)');
        $this->addSql('ALTER TABLE video ADD recurso_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CE52B6C4E FOREIGN KEY (recurso_id) REFERENCES recurso (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC7DA2CE52B6C4E ON video (recurso_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE favoritos');
        $this->addSql('DROP TABLE acceso');
        $this->addSql('ALTER TABLE audio DROP FOREIGN KEY FK_187D3695E52B6C4E');
        $this->addSql('DROP INDEX UNIQ_187D3695E52B6C4E ON audio');
        $this->addSql('ALTER TABLE audio DROP recurso_id');
        $this->addSql('ALTER TABLE imagen DROP FOREIGN KEY FK_8319D2B3E52B6C4E');
        $this->addSql('DROP INDEX UNIQ_8319D2B3E52B6C4E ON imagen');
        $this->addSql('ALTER TABLE imagen DROP recurso_id');
        $this->addSql('ALTER TABLE modelo3d DROP FOREIGN KEY FK_B78F45FE52B6C4E');
        $this->addSql('DROP INDEX UNIQ_B78F45FE52B6C4E ON modelo3d');
        $this->addSql('ALTER TABLE modelo3d DROP recurso_id');
        $this->addSql('ALTER TABLE recurso DROP FOREIGN KEY FK_B2BB376453C8D32C');
        $this->addSql('DROP INDEX IDX_B2BB376453C8D32C ON recurso');
        $this->addSql('ALTER TABLE recurso DROP propietario_id');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05DA354F9DC');
        $this->addSql('DROP INDEX IDX_2265B05DA354F9DC ON usuario');
        $this->addSql('ALTER TABLE usuario DROP tier_id');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CE52B6C4E');
        $this->addSql('DROP INDEX UNIQ_7CC7DA2CE52B6C4E ON video');
        $this->addSql('ALTER TABLE video DROP recurso_id');
    }
}
