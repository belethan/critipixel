<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240422185649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration complète MySQL alignée avec l’ancien schéma PostgreSQL + entités';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        ////////////////////////////////////////////////////////////////////////
        // MODE SQLITE : version simplifiée, SANS index, SANS foreign keys strict
        ////////////////////////////////////////////////////////////////////////
        if ($platform === 'sqlite') {

            // TAG
            $this->addSql('
                CREATE TABLE tag (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    code VARCHAR(255) NOT NULL,
                    name VARCHAR(30) NOT NULL
                );
            ');

            // USER
            $this->addSql('
                CREATE TABLE user (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username VARCHAR(30) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    password VARCHAR(60) NOT NULL
                );
            ');

            // VIDEO_GAME
            $this->addSql('
                CREATE TABLE video_game (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title VARCHAR(100) NOT NULL,
                    image_name VARCHAR(255) DEFAULT NULL,
                    image_size INTEGER DEFAULT NULL,
                    slug VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    release_date DATE NOT NULL,
                    updated_at DATETIME DEFAULT NULL,
                    test TEXT DEFAULT NULL,
                    rating INTEGER DEFAULT NULL,
                    average_rating INTEGER DEFAULT NULL,
                    number_of_ratings_per_value_number_of_one   INTEGER NOT NULL,
                    number_of_ratings_per_value_number_of_two   INTEGER NOT NULL,
                    number_of_ratings_per_value_number_of_three INTEGER NOT NULL,
                    number_of_ratings_per_value_number_of_four  INTEGER NOT NULL,
                    number_of_ratings_per_value_number_of_five  INTEGER NOT NULL
                );
            ');

            // REVIEW (pas de clés étrangères strictes en SQLite)
            $this->addSql('
                CREATE TABLE review (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    video_game_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    rating INTEGER NOT NULL,
                    comment TEXT DEFAULT NULL
                );
            ');

            // PIVOT video_game_tags
            $this->addSql('
                CREATE TABLE video_game_tags (
                    video_game_id INTEGER NOT NULL,
                    tag_id INTEGER NOT NULL,
                    PRIMARY KEY(video_game_id, tag_id)
                );
            ');

            return; // IMPORTANT : on sort, on ne fait pas la version MySQL
        }

        ////////////////////////////////////////////////////////////////////////
        // MODE MYSQL : version complète avec index, InnoDB, charset, FK
        ////////////////////////////////////////////////////////////////////////

        // TAG
        $this->addSql('
            CREATE TABLE tag (
                id INT AUTO_INCREMENT NOT NULL,
                code VARCHAR(255) NOT NULL,
                name VARCHAR(30) NOT NULL,
                UNIQUE INDEX UNIQ_TAG_CODE (code),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE `utf8mb4_unicode_ci`
              ENGINE = InnoDB;
        ');

        // USER
        $this->addSql('
            CREATE TABLE user (
                id INT AUTO_INCREMENT NOT NULL,
                username VARCHAR(30) NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(60) NOT NULL,
                UNIQUE INDEX UNIQ_USER_USERNAME (username),
                UNIQUE INDEX UNIQ_USER_EMAIL (email),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE `utf8mb4_unicode_ci`
              ENGINE = InnoDB;
        ');

        // VIDEO_GAME
        $this->addSql('
            CREATE TABLE video_game (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(100) NOT NULL,
                image_name VARCHAR(255) DEFAULT NULL,
                image_size INT DEFAULT NULL,
                slug VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                release_date DATE NOT NULL,
                updated_at DATETIME DEFAULT NULL,
                test TEXT DEFAULT NULL,
                rating INT DEFAULT NULL,
                average_rating INT DEFAULT NULL,

                number_of_ratings_per_value_number_of_one   INT NOT NULL,
                number_of_ratings_per_value_number_of_two   INT NOT NULL,
                number_of_ratings_per_value_number_of_three INT NOT NULL,
                number_of_ratings_per_value_number_of_four  INT NOT NULL,
                number_of_ratings_per_value_number_of_five  INT NOT NULL,

                UNIQUE INDEX UNIQ_VIDEO_GAME_SLUG (slug),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE `utf8mb4_unicode_ci`
              ENGINE = InnoDB;
        ');

        // REVIEW
        $this->addSql('
            CREATE TABLE review (
                id INT AUTO_INCREMENT NOT NULL,
                video_game_id INT NOT NULL,
                user_id INT NOT NULL,
                rating INT NOT NULL,
                comment TEXT DEFAULT NULL,

                INDEX IDX_REVIEW_VIDEO_GAME (video_game_id),
                INDEX IDX_REVIEW_USER (user_id),

                PRIMARY KEY(id),

                CONSTRAINT FK_REVIEW_VIDEO_GAME
                    FOREIGN KEY (video_game_id)
                    REFERENCES video_game (id)
                    ON DELETE CASCADE,

                CONSTRAINT FK_REVIEW_USER
                    FOREIGN KEY (user_id)
                    REFERENCES user (id)
                    ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE `utf8mb4_unicode_ci`
              ENGINE = InnoDB;
        ');

        // VIDEO_GAME_TAGS
        $this->addSql('
            CREATE TABLE video_game_tags (
                video_game_id INT NOT NULL,
                tag_id INT NOT NULL,

                INDEX IDX_VGT_VIDEO_GAME (video_game_id),
                INDEX IDX_VGT_TAG (tag_id),

                PRIMARY KEY(video_game_id, tag_id),

                CONSTRAINT FK_VGT_VIDEO_GAME
                    FOREIGN KEY (video_game_id)
                    REFERENCES video_game (id)
                    ON DELETE CASCADE,

                CONSTRAINT FK_VGT_TAG
                    FOREIGN KEY (tag_id)
                    REFERENCES tag (id)
                    ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE `utf8mb4_unicode_ci`
              ENGINE = InnoDB;
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE video_game_tags');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE video_game');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE tag');
    }
}
