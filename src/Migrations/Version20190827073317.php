<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190827073317 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE oauth2_client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', secret VARCHAR(255) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_454D96735F37A13B (token), INDEX IDX_454D967319EB6921 (client_id), INDEX IDX_454D9673A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', last_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, email_confirmed_at DATETIME DEFAULT NULL, need_email_confirm TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, uuid VARCHAR(36) NOT NULL, username VARCHAR(255) DEFAULT NULL, username_canonical VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), UNIQUE INDEX UNIQ_8D93D649D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_refresh_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_4DD907325F37A13B (token), INDEX IDX_4DD9073219EB6921 (client_id), INDEX IDX_4DD90732A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sonata_gallery (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, context VARCHAR(64) NOT NULL, default_format VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sonata_media (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, provider_name VARCHAR(255) NOT NULL, provider_status INT NOT NULL, provider_reference VARCHAR(255) NOT NULL, provider_metadata LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', width INT DEFAULT NULL, height INT DEFAULT NULL, length NUMERIC(10, 0) DEFAULT NULL, content_type VARCHAR(255) DEFAULT NULL, content_size INT DEFAULT NULL, copyright VARCHAR(255) DEFAULT NULL, author_name VARCHAR(255) DEFAULT NULL, context VARCHAR(64) DEFAULT NULL, cdn_is_flushable TINYINT(1) DEFAULT NULL, cdn_flush_identifier VARCHAR(64) DEFAULT NULL, cdn_flush_at DATETIME DEFAULT NULL, cdn_status INT DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, hash VARCHAR(255) DEFAULT NULL, origin_name VARCHAR(255) DEFAULT NULL, uuid VARCHAR(36) NOT NULL, UNIQUE INDEX UNIQ_3B1662B9D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sonata_gallery_media (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, media_id INT DEFAULT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_4DC0EE4A4E7AF8F (gallery_id), INDEX IDX_4DC0EE4AEA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_auth_code (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1D2905B55F37A13B (token), INDEX IDX_1D2905B519EB6921 (client_id), INDEX IDX_1D2905B5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D967319EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (id)');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D9673A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE oauth2_refresh_token ADD CONSTRAINT FK_4DD9073219EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (id)');
        $this->addSql('ALTER TABLE oauth2_refresh_token ADD CONSTRAINT FK_4DD90732A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sonata_gallery_media ADD CONSTRAINT FK_4DC0EE4A4E7AF8F FOREIGN KEY (gallery_id) REFERENCES sonata_gallery (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sonata_gallery_media ADD CONSTRAINT FK_4DC0EE4AEA9FDD75 FOREIGN KEY (media_id) REFERENCES sonata_media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_auth_code ADD CONSTRAINT FK_1D2905B519EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (id)');
        $this->addSql('ALTER TABLE oauth2_auth_code ADD CONSTRAINT FK_1D2905B5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oauth2_access_token DROP FOREIGN KEY FK_454D967319EB6921');
        $this->addSql('ALTER TABLE oauth2_refresh_token DROP FOREIGN KEY FK_4DD9073219EB6921');
        $this->addSql('ALTER TABLE oauth2_auth_code DROP FOREIGN KEY FK_1D2905B519EB6921');
        $this->addSql('ALTER TABLE oauth2_access_token DROP FOREIGN KEY FK_454D9673A76ED395');
        $this->addSql('ALTER TABLE oauth2_refresh_token DROP FOREIGN KEY FK_4DD90732A76ED395');
        $this->addSql('ALTER TABLE oauth2_auth_code DROP FOREIGN KEY FK_1D2905B5A76ED395');
        $this->addSql('ALTER TABLE sonata_gallery_media DROP FOREIGN KEY FK_4DC0EE4A4E7AF8F');
        $this->addSql('ALTER TABLE sonata_gallery_media DROP FOREIGN KEY FK_4DC0EE4AEA9FDD75');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE oauth2_client');
        $this->addSql('DROP TABLE oauth2_access_token');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE oauth2_refresh_token');
        $this->addSql('DROP TABLE sonata_gallery');
        $this->addSql('DROP TABLE sonata_media');
        $this->addSql('DROP TABLE sonata_gallery_media');
        $this->addSql('DROP TABLE oauth2_auth_code');
    }
}
