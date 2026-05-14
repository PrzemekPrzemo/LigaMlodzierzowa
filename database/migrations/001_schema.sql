-- Schemat bazy danych: Liga Młodzieżowa PZSS 2026 (do lat 17)
-- Zgodny z regulaminem PZSS: rozgrywka ZESPOŁOWA, 2 konkurencje (KPn, PPn), 4 rundy eliminacyjne, top 8 → Finał.
-- MySQL/MariaDB, PHP 8.3+, kodowanie utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS editions (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    year         SMALLINT UNSIGNED NOT NULL UNIQUE,
    slug         VARCHAR(64) NOT NULL UNIQUE,
    title        VARCHAR(160) NOT NULL,
    subtitle     VARCHAR(255) NULL,
    description  TEXT NULL,
    age_limit    VARCHAR(40) NOT NULL DEFAULT 'rocznik 2007 i młodsi',
    organizer    VARCHAR(160) NOT NULL DEFAULT 'Polski Związek Strzelectwa Sportowego',
    pzss_url     VARCHAR(255) NULL,
    results_pdf  VARCHAR(255) NULL,
    regulation_pdf VARCHAR(255) NULL,
    state_as_of  DATE NULL,
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS disciplines (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code      VARCHAR(16) NOT NULL UNIQUE,        -- KPN, PPN
    name      VARCHAR(120) NOT NULL,              -- Karabin pneumatyczny
    short     VARCHAR(32) NOT NULL,
    icon      VARCHAR(64) NULL,
    sort      INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rounds (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    edition_id   INT UNSIGNED NOT NULL,
    number       TINYINT UNSIGNED NOT NULL,             -- I..IV oraz 99 dla finału
    code         VARCHAR(32) NOT NULL,                  -- ZLOT, PB, PP_PZSS, ZM_ZK, FINAL
    label        VARCHAR(120) NOT NULL,
    short_label  VARCHAR(48) NOT NULL,
    starts_on    DATE NULL,
    ends_on      DATE NULL,
    venue        VARCHAR(160) NULL,
    city         VARCHAR(80) NULL,
    host_club    VARCHAR(160) NULL,
    is_final     TINYINT(1) NOT NULL DEFAULT 0,
    status       ENUM('planned','ongoing','finished') NOT NULL DEFAULT 'planned',
    sort         INT NOT NULL DEFAULT 0,
    UNIQUE KEY uq_round (edition_id, code),
    CONSTRAINT fk_rounds_edition FOREIGN KEY (edition_id) REFERENCES editions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS clubs (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(160) NOT NULL,            -- np. "ZAWISZA Bydgoszcz"
    short     VARCHAR(80) NULL,                 -- np. "ZAWISZA"
    city      VARCHAR(80) NULL,
    region    VARCHAR(80) NULL,
    UNIQUE KEY uq_club (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS teams (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    edition_id    INT UNSIGNED NOT NULL,
    club_id       INT UNSIGNED NOT NULL,
    discipline_id INT UNSIGNED NOT NULL,
    display_name  VARCHAR(160) NOT NULL,        -- np. "ZAWISZA Bydgoszcz"
    UNIQUE KEY uq_team (edition_id, club_id, discipline_id),
    CONSTRAINT fk_team_edition    FOREIGN KEY (edition_id)    REFERENCES editions(id)    ON DELETE CASCADE,
    CONSTRAINT fk_team_club       FOREIGN KEY (club_id)       REFERENCES clubs(id)       ON DELETE CASCADE,
    CONSTRAINT fk_team_discipline FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS team_scores (
    id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id   INT UNSIGNED NOT NULL,
    round_id  INT UNSIGNED NOT NULL,
    score     DECIMAL(7,2) NOT NULL,            -- np. 1243.10 (karabin), 1105 (pistolet)
    note      VARCHAR(120) NULL,
    UNIQUE KEY uq_score (team_id, round_id),
    INDEX ix_round_score (round_id, score DESC),
    CONSTRAINT fk_score_team  FOREIGN KEY (team_id)  REFERENCES teams(id)  ON DELETE CASCADE,
    CONSTRAINT fk_score_round FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bieżące rozstawienie do Finału (top 8) — łatwy podgląd, generowane automatycznie z najlepszych startów.
CREATE OR REPLACE VIEW v_standings AS
SELECT
    t.edition_id,
    t.discipline_id,
    d.code   AS discipline_code,
    d.name   AS discipline_name,
    t.id     AS team_id,
    t.display_name AS team_name,
    MAX(ts.score) AS best_score,
    COUNT(ts.id)  AS rounds_played
FROM teams t
LEFT JOIN team_scores ts ON ts.team_id = t.id
JOIN disciplines d ON d.id = t.discipline_id
GROUP BY t.id, d.id;

CREATE TABLE IF NOT EXISTS news (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    edition_id  INT UNSIGNED NULL,
    title       VARCHAR(200) NOT NULL,
    slug        VARCHAR(200) NOT NULL UNIQUE,
    lead        VARCHAR(500) NULL,
    body        MEDIUMTEXT NULL,
    cover       VARCHAR(255) NULL,
    is_pinned   TINYINT(1) NOT NULL DEFAULT 0,
    published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_news_edition FOREIGN KEY (edition_id) REFERENCES editions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS documents (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    edition_id  INT UNSIGNED NULL,
    title       VARCHAR(200) NOT NULL,
    kind        ENUM('regulamin','wyniki','komunikat','formularz','inny') NOT NULL DEFAULT 'inny',
    url         VARCHAR(500) NOT NULL,
    source      VARCHAR(80) NULL DEFAULT 'PZSS',
    published_at DATE NULL,
    sort        INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_doc_edition FOREIGN KEY (edition_id) REFERENCES editions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS partners (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(160) NOT NULL,
    role    VARCHAR(80) NULL,
    logo    VARCHAR(255) NULL,
    url     VARCHAR(255) NULL,
    sort    INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
