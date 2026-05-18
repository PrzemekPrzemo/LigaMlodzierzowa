-- 007 — Pole "dyscyplina główna" w athletes (idempotentne)

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'athletes' AND COLUMN_NAME = 'primary_discipline');
SET @sql := IF(@c = 0,
    "ALTER TABLE athletes ADD COLUMN primary_discipline ENUM('KPN','PPN','BOTH') NULL AFTER gender",
    'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
