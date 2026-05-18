-- 008 — Kategoria partnera + linki social (idempotentne)

-- sponsors.category
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sponsors' AND COLUMN_NAME = 'category');
SET @sql := IF(@c = 0,
    "ALTER TABLE sponsors ADD COLUMN category ENUM('patronat_honorowy','sponsor_glowny','sponsor','partner','partner_medialny','partner_techniczny') NOT NULL DEFAULT 'partner' AFTER tier",
    'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- sponsors.instagram_url
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sponsors' AND COLUMN_NAME = 'instagram_url');
SET @sql := IF(@c = 0,
    "ALTER TABLE sponsors ADD COLUMN instagram_url VARCHAR(255) NULL AFTER url",
    'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- sponsors.facebook_url
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sponsors' AND COLUMN_NAME = 'facebook_url');
SET @sql := IF(@c = 0,
    "ALTER TABLE sponsors ADD COLUMN facebook_url VARCHAR(255) NULL AFTER instagram_url",
    'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Migracja: stary tier='patronat' -> category='patronat_honorowy'
UPDATE sponsors SET category = 'patronat_honorowy' WHERE tier = 'patronat' AND category = 'partner';
