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
        //
        // TAG
        //
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

        //
        // USER
        //
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

        //
        // VIDEO GAME
        //
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

        //
        // REVIEW
        //
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

        //
        // VIDEO GAME TAGS (Pivot)
        //
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
        // Toujours dans le bon ordre
        $this->addSql('DROP TABLE video_game_tags');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE video_game');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE tag');
    }
}
