-- 003 — Slugi klubów + przykładowi zawodnicy zespołu Zawisza (KPN, edycja 2026)

UPDATE clubs SET slug = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
    name, ' ', '-'), 'ą','a'), 'ć','c'), 'ę','e'), 'ł','l'), 'ń','n'), 'ó','o'), 'ś','s'))
WHERE slug IS NULL OR slug = '';

UPDATE clubs SET slug = REPLACE(REPLACE(REPLACE(slug,'ź','z'),'ż','z'),'/','-');

-- Demo: 4 zawodników klubu ZAWISZA Bydgoszcz (karabin pneumatyczny)
SET @club_zawisza := (SELECT id FROM clubs WHERE name = 'ZAWISZA Bydgoszcz' LIMIT 1);

INSERT IGNORE INTO athletes (club_id, first_name, last_name, birth_year, gender, slug) VALUES
 (@club_zawisza, 'Antoni',  'Nowak',     2010, 'M', 'antoni-nowak-zawisza'),
 (@club_zawisza, 'Maja',    'Kowalska',  2010, 'K', 'maja-kowalska-zawisza'),
 (@club_zawisza, 'Jakub',   'Wiśniewski',2011, 'M', 'jakub-wisniewski-zawisza'),
 (@club_zawisza, 'Aleksandra','Wójcik',  2011, 'K', 'aleksandra-wojcik-zawisza');

SET @ed_2026 := (SELECT id FROM editions WHERE year = 2026);
SET @d_kpn   := (SELECT id FROM disciplines WHERE code = 'KPN');
SET @r1      := (SELECT id FROM rounds WHERE edition_id = @ed_2026 AND code = 'ZLOT');
SET @team_zawisza_kpn := (SELECT id FROM teams WHERE edition_id = @ed_2026 AND club_id = @club_zawisza AND discipline_id = @d_kpn LIMIT 1);

-- Demo: trzech zawodników strzelało na ZLOT Orlików (suma 1243,1 z PDF)
INSERT INTO athlete_scores (athlete_id, round_id, discipline_id, team_id, score, counts_in_team)
SELECT a.id, @r1, @d_kpn, @team_zawisza_kpn, sc.score, 1 FROM (
    SELECT 'Antoni'    AS fn, 'Nowak'      AS ln, 415.40 AS score UNION ALL
    SELECT 'Maja',        'Kowalska',                 414.80 UNION ALL
    SELECT 'Jakub',       'Wiśniewski',               412.90
) sc
JOIN athletes a ON a.club_id = @club_zawisza AND a.first_name = sc.fn AND a.last_name = sc.ln
ON DUPLICATE KEY UPDATE score = VALUES(score);
