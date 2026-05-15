-- Seed: Liga Młodzieżowa PZSS 2026 — dane na podstawie PDF PZSS (stan 09.04.2026)

INSERT INTO editions (year, slug, title, subtitle, description, age_limit, organizer,
                      pzss_url, results_pdf, regulation_pdf, state_as_of)
VALUES (
    2026,
    'liga-mlodziezowa-2026',
    'Liga Młodzieżowa PZSS 2026',
    'Ogólnopolski cykl zawodów zespołowych do lat 17 — karabin i pistolet pneumatyczny',
    'Liga Młodzieżowa PZSS to system rozgrywek klubów biorących udział we współzawodnictwie dzieci i młodzieży w strzelectwie sportowym. Cele: popularyzacja strzelectwa wśród dzieci i młodzieży, podniesienie poziomu sportowego oraz przygotowanie zawodników do Finału Ligi Europejskiej ESC. Zespół trzyosobowy z jednego klubu (możliwy mieszany) rywalizuje w 4 rundach eliminacyjnych. Do Finału kwalifikuje się 8 najlepszych zespołów w każdej z konkurencji — według najwyższego wyniku ze wszystkich rund. Finał edycji 2026 rozegrany zostanie wspólnie ze Strzeleckim Pucharem Gdyni.',
    'rocznik 2007 i młodsi',
    'Polski Związek Strzelectwa Sportowego',
    'https://www.pzss.org.pl/szkolenie/liga-mlodziezowa/liga-mlodziezowa-2026',
    'https://www.pzss.org.pl/assets/files/liga-mlodziezowa-pzss-2026.pdf',
    'https://www.pzss.org.pl/assets/files/zawody-i-organizacja/regulamin-final-ligi-mlodziezowej-2026.pdf',
    '2026-04-09'
)
ON DUPLICATE KEY UPDATE
    title=VALUES(title), subtitle=VALUES(subtitle), description=VALUES(description),
    pzss_url=VALUES(pzss_url), results_pdf=VALUES(results_pdf),
    regulation_pdf=VALUES(regulation_pdf), state_as_of=VALUES(state_as_of);

SET @ed := (SELECT id FROM editions WHERE year = 2026);

INSERT IGNORE INTO disciplines (code, name, short, icon, sort) VALUES
 ('KPN', 'Karabin pneumatyczny',  'KPn', 'rifle',  10),
 ('PPN', 'Pistolet pneumatyczny', 'PPn', 'pistol', 20);

SET @d_kpn := (SELECT id FROM disciplines WHERE code = 'KPN');
SET @d_ppn := (SELECT id FROM disciplines WHERE code = 'PPN');

INSERT IGNORE INTO rounds (edition_id, number, code, label, short_label, city, host_club, venue, is_final, status, sort) VALUES
 (@ed, 1, 'ZLOT',    'Zlot Orlików',                     'ZLOT Orlików Siedlce',   'Siedlce',   NULL, 'Siedlce',   0, 'finished', 10),
 (@ed, 2, 'PB',      'Puchar Bydgoszczy',                'Puchar Bydgoszczy',      'Bydgoszcz', NULL, 'Bydgoszcz', 0, 'planned',  20),
 (@ed, 3, 'PP_PZSS', 'Puchar Prezesa PZSS',              'PP PZSS Bydgoszcz',      'Bydgoszcz', NULL, 'Bydgoszcz', 0, 'planned',  30),
 (@ed, 4, 'ZM_ZK',   'Złoty Muszkiet / Złota Krócica',   'ZM ZK Wrocław',          'Wrocław',   NULL, 'Wrocław',   0, 'planned',  40),
 (@ed,99, 'FINAL',   'Finał Ligi Młodzieżowej PZSS 2026 / Strzelecki Puchar Gdyni', 'Finał — Gdynia',
                                                                                   'Gdynia',    'Strzelecki Puchar Gdyni', 'Strzelnica Gdynia', 1, 'planned', 99);

-- Kluby (na podstawie PDF wyników)
INSERT IGNORE INTO clubs (name, short, city) VALUES
 ('DELFIN Tarnów',        'DELFIN',        'Tarnów'),
 ('KALIBER Białystok',    'KALIBER',       'Białystok'),
 ('ZKS Warszawa',         'ZKS',           'Warszawa'),
 ('ZAWISZA Bydgoszcz',    'ZAWISZA',       'Bydgoszcz'),
 ('ŚLĄSK Wrocław',        'ŚLĄSK',         'Wrocław'),
 ('FLOTA Gdynia',         'FLOTA',         'Gdynia'),
 ('SOKÓŁ Zduńska Wola',   'SOKÓŁ',         'Zduńska Wola'),
 ('LEGIA Warszawa',       'LEGIA',         'Warszawa'),
 ('PROMIEŃ Bochnia',      'PROMIEŃ',       'Bochnia'),
 ('PRECYZJA Kraków',      'PRECYZJA',      'Kraków'),
 ('GROT Puck',            'GROT',          'Puck'),
 ('LIDER-AMICUS Lębork',  'LIDER-AMICUS',  'Lębork'),
 ('GWARDIA Zielona Góra', 'GWARDIA',       'Zielona Góra'),
 ('TKS LOK Tarnów',       'TKS LOK',       'Tarnów'),
 ('HUSARIA Krosno',       'HUSARIA',       'Krosno'),
 ('DZIESIĄTKA Łódź',      'DZIESIĄTKA',    'Łódź'),
 ('GRYF Słupsk',          'GRYF',          'Słupsk'),
 ('AZS Częstochowa',      'AZS',           'Częstochowa');

-- Zespoły (klub × dyscyplina, edycja 2026)
INSERT IGNORE INTO teams (edition_id, club_id, discipline_id, display_name)
SELECT @ed, c.id, @d_ppn, c.name
FROM clubs c
WHERE c.name IN ('DELFIN Tarnów','KALIBER Białystok','ZKS Warszawa','ZAWISZA Bydgoszcz',
                 'ŚLĄSK Wrocław','FLOTA Gdynia','SOKÓŁ Zduńska Wola','LEGIA Warszawa',
                 'PROMIEŃ Bochnia','PRECYZJA Kraków','GROT Puck','LIDER-AMICUS Lębork');

INSERT IGNORE INTO teams (edition_id, club_id, discipline_id, display_name)
SELECT @ed, c.id, @d_kpn, c.name
FROM clubs c
WHERE c.name IN ('ZAWISZA Bydgoszcz','LIDER-AMICUS Lębork','GWARDIA Zielona Góra','TKS LOK Tarnów',
                 'HUSARIA Krosno','KALIBER Białystok','ŚLĄSK Wrocław','SOKÓŁ Zduńska Wola',
                 'LEGIA Warszawa','DZIESIĄTKA Łódź','FLOTA Gdynia','GRYF Słupsk',
                 'ZKS Warszawa','AZS Częstochowa');

-- Wyniki rundy I (ZLOT Orlików Siedlce) — pistolet
SET @r1 := (SELECT id FROM rounds WHERE edition_id = @ed AND code = 'ZLOT');

INSERT INTO team_scores (team_id, round_id, score)
SELECT t.id, @r1, s.score FROM (
    SELECT 'DELFIN Tarnów'       AS team, 1105.00 AS score UNION ALL
    SELECT 'KALIBER Białystok',    1089.00 UNION ALL
    SELECT 'ZKS Warszawa',         1078.00 UNION ALL
    SELECT 'ZAWISZA Bydgoszcz',    1073.00 UNION ALL
    SELECT 'ŚLĄSK Wrocław',        1072.00 UNION ALL
    SELECT 'FLOTA Gdynia',         1067.00 UNION ALL
    SELECT 'SOKÓŁ Zduńska Wola',   1066.00 UNION ALL
    SELECT 'LEGIA Warszawa',       1048.00 UNION ALL
    SELECT 'PROMIEŃ Bochnia',      1037.00 UNION ALL
    SELECT 'PRECYZJA Kraków',      1035.00 UNION ALL
    SELECT 'GROT Puck',            1034.00 UNION ALL
    SELECT 'LIDER-AMICUS Lębork',  1030.00
) s
JOIN teams t  ON t.display_name = s.team AND t.edition_id = @ed AND t.discipline_id = @d_ppn
ON DUPLICATE KEY UPDATE score = VALUES(score);

-- Wyniki rundy I (ZLOT Orlików Siedlce) — karabin
INSERT INTO team_scores (team_id, round_id, score)
SELECT t.id, @r1, s.score FROM (
    SELECT 'ZAWISZA Bydgoszcz'    AS team, 1243.10 AS score UNION ALL
    SELECT 'LIDER-AMICUS Lębork',   1231.30 UNION ALL
    SELECT 'GWARDIA Zielona Góra',  1230.40 UNION ALL
    SELECT 'TKS LOK Tarnów',        1227.10 UNION ALL
    SELECT 'HUSARIA Krosno',        1217.40 UNION ALL
    SELECT 'KALIBER Białystok',     1202.00 UNION ALL
    SELECT 'ŚLĄSK Wrocław',         1201.10 UNION ALL
    SELECT 'SOKÓŁ Zduńska Wola',    1189.80 UNION ALL
    SELECT 'LEGIA Warszawa',        1183.50 UNION ALL
    SELECT 'DZIESIĄTKA Łódź',       1182.20 UNION ALL
    SELECT 'FLOTA Gdynia',          1180.50 UNION ALL
    SELECT 'GRYF Słupsk',           1163.70 UNION ALL
    SELECT 'ZKS Warszawa',          1153.20 UNION ALL
    SELECT 'AZS Częstochowa',       1105.60
) s
JOIN teams t  ON t.display_name = s.team AND t.edition_id = @ed AND t.discipline_id = @d_kpn
ON DUPLICATE KEY UPDATE score = VALUES(score);

-- Idempotentne: usuwamy stare wpisy zarządzane przez seed, potem wstawiamy świeże
DELETE FROM documents WHERE edition_id = @ed AND url IN (
    'https://www.pzss.org.pl/assets/files/liga-mlodziezowa-pzss-2026.pdf',
    'https://www.pzss.org.pl/assets/files/zawody-i-organizacja/regulamin-final-ligi-mlodziezowej-2026.pdf',
    'https://www.pzss.org.pl/szkolenie/liga-mlodziezowa/liga-mlodziezowa-2026'
);
INSERT INTO documents (edition_id, title, kind, url, source, published_at, sort) VALUES
 (@ed, 'Aktualne wyniki Ligi Młodzieżowej 2026 (PDF)',  'wyniki',    'https://www.pzss.org.pl/assets/files/liga-mlodziezowa-pzss-2026.pdf', 'PZSS', '2026-04-09', 10),
 (@ed, 'Regulamin Finału Ligi Młodzieżowej 2026 (PDF)', 'regulamin', 'https://www.pzss.org.pl/assets/files/zawody-i-organizacja/regulamin-final-ligi-mlodziezowej-2026.pdf', 'PZSS', '2026-01-10', 20),
 (@ed, 'Strona Ligi Młodzieżowej w serwisie PZSS',      'inny',      'https://www.pzss.org.pl/szkolenie/liga-mlodziezowa/liga-mlodziezowa-2026', 'PZSS', '2026-01-01', 30);

INSERT IGNORE INTO news (edition_id, title, slug, lead, body, is_pinned, published_at) VALUES
 (@ed,
  'Po I rundzie Ligi Młodzieżowej PZSS 2026 — DELFIN i ZAWISZA na czele',
  'po-rundzie-zlot-orlikow-2026',
  'Pierwszą rundę eliminacyjną rozegrano w ramach Zlotu Orlików w Siedlcach. W pistolecie pneumatycznym najlepszy wynik osiągnął DELFIN Tarnów (1105), a w karabinie pneumatycznym — ZAWISZA Bydgoszcz (1243,1).',
  '<p>Za nami pierwsza z czterech rund eliminacyjnych Ligi Młodzieżowej PZSS 2026. <strong>W pistolecie pneumatycznym</strong> prowadzenie objął DELFIN Tarnów (1105 pkt), przed KALIBREM Białystok (1089) i ZKS Warszawa (1078). <strong>W karabinie pneumatycznym</strong> najwyższy wynik osiągnęła ZAWISZA Bydgoszcz (1243,1), wyprzedzając LIDER-AMICUS Lębork (1231,3) i GWARDIĘ Zielona Góra (1230,4).</p><p>Do Finału Ligi Młodzieżowej awansuje 8 najlepszych zespołów w każdej z konkurencji — według najwyższego wyniku osiągniętego w którejkolwiek z czterech rund eliminacyjnych.</p><p>Kolejna runda: <strong>Puchar Bydgoszczy</strong>.</p>',
  1, '2026-04-09 18:00:00'),
 (@ed,
  'Finał Ligi Młodzieżowej 2026 połączony ze Strzeleckim Pucharem Gdyni',
  'final-puchar-gdyni-2026',
  'Decyzją PZSS Finał sezonu 2026 zostanie rozegrany w Gdyni jako wspólne zawody Ligi i Strzeleckiego Pucharu Gdyni.',
  '<p>Finał Ligi Młodzieżowej PZSS 2026 odbędzie się w formule trzydniowej: dwa dni meczów kwalifikacyjnych w grupach (G1: 1, 4, 5, 8 oraz G2: 2, 3, 6, 7) oraz dzień meczów medalowych. Każda potyczka to bezpośrednie pojedynki zawodników z tym samym numerem rankingowym w zespole.</p><p>W tym sezonie finał zostanie połączony ze <strong>Strzeleckim Pucharem Gdyni</strong>, co podniesie rangę i atrakcyjność wydarzenia.</p>',
  0, '2026-02-02 12:00:00');

DELETE FROM partners WHERE name IN (
    'Polski Związek Strzelectwa Sportowego',
    'Liga Młodzieżowa PZSS',
    'Strzelecki Puchar Gdyni',
    'Ministerstwo Sportu i Turystyki'
);
INSERT INTO partners (name, role, logo, url, sort) VALUES
 ('Polski Związek Strzelectwa Sportowego', 'organizator', '/assets/img/logo-pzss.svg',          'https://www.pzss.org.pl', 10),
 ('Liga Młodzieżowa PZSS',                 'cykl',        '/assets/img/logo-liga.svg',          'https://www.pzss.org.pl/szkolenie/liga-mlodziezowa/liga-mlodziezowa-2026', 20),
 ('Strzelecki Puchar Gdyni',               'finał',       '/assets/img/logo-puchar-gdyni.svg',  '#', 30);
