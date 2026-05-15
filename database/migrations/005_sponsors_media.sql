-- 005 — Sponsorzy, galeria, transmisje live

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS sponsors (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    edition_id  INT UNSIGNED NULL,
    name        VARCHAR(160) NOT NULL,
    tier        ENUM('zloto','srebro','braz','partner','patronat') NOT NULL DEFAULT 'partner',
    scope       ENUM('liga','spg','final','wszystko') NOT NULL DEFAULT 'wszystko',
    logo        VARCHAR(255) NULL,
    url         VARCHAR(255) NULL,
    description VARCHAR(500) NULL,
    sort        INT NOT NULL DEFAULT 0,
    is_visible  TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT fk_sponsor_edition FOREIGN KEY (edition_id) REFERENCES editions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    edition_id  INT UNSIGNED NULL,
    round_id    INT UNSIGNED NULL,
    title       VARCHAR(200) NULL,
    image_url   VARCHAR(500) NOT NULL,
    thumb_url   VARCHAR(500) NULL,
    caption     VARCHAR(500) NULL,
    sort        INT NOT NULL DEFAULT 0,
    published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_gallery_edition FOREIGN KEY (edition_id) REFERENCES editions(id) ON DELETE SET NULL,
    CONSTRAINT fk_gallery_round   FOREIGN KEY (round_id)   REFERENCES rounds(id)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS live_streams (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    edition_id  INT UNSIGNED NULL,
    round_id    INT UNSIGNED NULL,
    title       VARCHAR(200) NOT NULL,
    platform    ENUM('youtube','twitch','facebook','inny') NOT NULL DEFAULT 'youtube',
    embed_url   VARCHAR(500) NOT NULL,
    starts_at   DATETIME NULL,
    status      ENUM('upcoming','live','ended') NOT NULL DEFAULT 'upcoming',
    sort        INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_live_edition FOREIGN KEY (edition_id) REFERENCES editions(id) ON DELETE SET NULL,
    CONSTRAINT fk_live_round   FOREIGN KEY (round_id)   REFERENCES rounds(id)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
