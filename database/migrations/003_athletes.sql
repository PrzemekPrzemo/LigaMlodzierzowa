-- 003 — Zawodnicy, sloty zespołowe, archiwum edycji
-- Migracja idempotentna: można puszczać wielokrotnie bez błędów.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Helpery: warunkowe ALTERy przez information_schema + PREPARE/EXECUTE

-- clubs.slug
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clubs' AND COLUMN_NAME = 'slug');
SET @sql := IF(@c = 0,
    'ALTER TABLE clubs ADD COLUMN slug VARCHAR(80) NULL AFTER short',
    'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- clubs.logo
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clubs' AND COLUMN_NAME = 'logo');
SET @sql := IF(@c = 0,
    'ALTER TABLE clubs ADD COLUMN logo VARCHAR(255) NULL AFTER region',
    'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- clubs.website
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clubs' AND COLUMN_NAME = 'website');
SET @sql := IF(@c = 0,
    'ALTER TABLE clubs ADD COLUMN website VARCHAR(255) NULL AFTER logo',
    'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- clubs UNIQUE KEY uq_club_slug
SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'clubs' AND INDEX_NAME = 'uq_club_slug');
SET @sql := IF(@c = 0,
    'ALTER TABLE clubs ADD UNIQUE KEY uq_club_slug (slug)',
    'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- editions.edition_kind
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'editions' AND COLUMN_NAME = 'edition_kind');
SET @sql := IF(@c = 0,
    "ALTER TABLE editions ADD COLUMN edition_kind ENUM('current','archive') NOT NULL DEFAULT 'current' AFTER state_as_of",
    'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Tabele zawodników (CREATE TABLE IF NOT EXISTS — naturalnie idempotentne)

CREATE TABLE IF NOT EXISTS athletes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    club_id     INT UNSIGNED NULL,
    first_name  VARCHAR(80) NOT NULL,
    last_name   VARCHAR(80) NOT NULL,
    birth_year  SMALLINT UNSIGNED NULL,
    gender      ENUM('M','K') NULL,
    slug        VARCHAR(160) NULL,
    license_no  VARCHAR(40) NULL,
    UNIQUE KEY uq_athlete (club_id, last_name, first_name, birth_year),
    INDEX ix_athlete_name (last_name, first_name),
    CONSTRAINT fk_athlete_club FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS athlete_scores (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    athlete_id     INT UNSIGNED NOT NULL,
    round_id       INT UNSIGNED NOT NULL,
    discipline_id  INT UNSIGNED NOT NULL,
    team_id        INT UNSIGNED NULL,
    score          DECIMAL(7,2) NOT NULL,
    counts_in_team TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_ascore (athlete_id, round_id, discipline_id),
    INDEX ix_ascore_round_disc (round_id, discipline_id, score DESC),
    INDEX ix_ascore_team (team_id),
    CONSTRAINT fk_ascore_athlete    FOREIGN KEY (athlete_id)    REFERENCES athletes(id)    ON DELETE CASCADE,
    CONSTRAINT fk_ascore_round      FOREIGN KEY (round_id)      REFERENCES rounds(id)      ON DELETE CASCADE,
    CONSTRAINT fk_ascore_discipline FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE CASCADE,
    CONSTRAINT fk_ascore_team       FOREIGN KEY (team_id)       REFERENCES teams(id)       ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
