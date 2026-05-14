-- Program Finału + dane Strzeleckiego Pucharu Gdyni

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS round_events (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    round_id   INT UNSIGNED NOT NULL,
    day_no     TINYINT UNSIGNED NOT NULL DEFAULT 1,
    day_label  VARCHAR(80) NULL,
    time_start TIME NULL,
    time_end   TIME NULL,
    title      VARCHAR(200) NOT NULL,
    kind       ENUM('rejestracja','treningi','kwalifikacje','mecze','ceremonia','organizacyjne','inny') NOT NULL DEFAULT 'inny',
    location   VARCHAR(160) NULL,
    notes      VARCHAR(255) NULL,
    sort       INT NOT NULL DEFAULT 0,
    INDEX ix_event_day (round_id, day_no, sort),
    CONSTRAINT fk_event_round FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS venues (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug      VARCHAR(64) NOT NULL UNIQUE,
    name      VARCHAR(160) NOT NULL,
    address   VARCHAR(255) NULL,
    city      VARCHAR(80) NULL,
    lat       DECIMAL(9,6) NULL,
    lng       DECIMAL(9,6) NULL,
    map_url   VARCHAR(500) NULL,
    description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
