-- 004 — Archiwum: Liga Młodzieżowa PZSS 2025 (wyniki ostateczne, stan 27.06.2025)

INSERT INTO editions (year, slug, title, subtitle, description, age_limit, organizer,
                      state_as_of, edition_kind, is_active)
VALUES (
    2025, 'liga-mlodziezowa-2025',
    'Liga Młodzieżowa PZSS 2025',
    'Edycja archiwalna — wyniki ostateczne (stan 27.06.2025)',
    'Wyniki ostateczne 4 rund eliminacyjnych edycji 2025. Do Finału zakwalifikowało się po 8 najlepszych zespołów w karabinie i pistolecie pneumatycznym.',
    'rocznik 2007 i młodsi',
    'Polski Związek Strzelectwa Sportowego',
    '2025-06-27', 'archive', 0
) ON DUPLICATE KEY UPDATE title=VALUES(title), state_as_of=VALUES(state_as_of);

SET @ed25 := (SELECT id FROM editions WHERE year = 2025);
SET @d_kpn := (SELECT id FROM disciplines WHERE code = 'KPN');
SET @d_ppn := (SELECT id FROM disciplines WHERE code = 'PPN');

INSERT IGNORE INTO rounds (edition_id, number, code, label, short_label, city, status, sort) VALUES
 (@ed25, 1, 'ZLOT',    'Zlot Orlików',                   'ZLOT Orlików Siedlce', 'Siedlce',   'finished', 10),
 (@ed25, 2, 'PB',      'Puchar Bydgoszczy',              'Puchar Bydgoszczy',    'Bydgoszcz', 'finished', 20),
 (@ed25, 3, 'PP_PZSS', 'Puchar Prezesa PZSS',            'PP PZSS Wrocław',      'Wrocław',   'finished', 30),
 (@ed25, 4, 'ZM_ZK',   'Złoty Muszkiet / Złota Krócica', 'ZM ZK Bydgoszcz',      'Bydgoszcz', 'finished', 40);

-- Dodatkowe kluby z 2025
INSERT IGNORE INTO clubs (name, short, city, slug) VALUES
 ('COVER Warszawa',           'COVER',       'Warszawa', 'cover-warszawa'),
 ('PETARDA Kraków',           'PETARDA',     'Kraków',   'petarda-krakow'),
 ('TARCZA Goleniów',          'TARCZA',      'Goleniów', 'tarcza-goleniow'),
 ('AGAT Złotoryja',           'AGAT',        'Złotoryja','agat-zlotoryja'),
 ('CZAK Świdnica',            'CZAK',        'Świdnica', 'czak-swidnica'),
 ('DRAGON Chełm',             'DRAGON',      'Chełm',    'dragon-chelm'),
 ('SPOŁEM Łódź',              'SPOŁEM',      'Łódź',     'spolem-lodz'),
 ('BURSZTYN Kalisz',          'BURSZTYN',    'Kalisz',   'bursztyn-kalisz'),
 ('ORNECKIE SMOKI Orneta',    'ORNECKIE SMOKI','Orneta', 'orneckie-smoki-orneta');

-- ------- ZESPOŁY 2025 -------
-- PISTOLET (17 zespołów)
INSERT IGNORE INTO teams (edition_id, club_id, discipline_id, display_name)
SELECT @ed25, c.id, @d_ppn, c.name FROM clubs c
WHERE c.name IN (
    'KALIBER Białystok','DELFIN Tarnów','ŚLĄSK Wrocław','ZAWISZA Bydgoszcz',
    'COVER Warszawa','PETARDA Kraków','GWARDIA Zielona Góra','ZKS Warszawa',
    'FLOTA Gdynia','SOKÓŁ Zduńska Wola','GROT Puck','TARCZA Goleniów',
    'AGAT Złotoryja','LIDER-AMICUS Lębork','LEGIA Warszawa','CZAK Świdnica',
    'PRECYZJA Kraków'
);

-- KARABIN (17 zespołów)
INSERT IGNORE INTO teams (edition_id, club_id, discipline_id, display_name)
SELECT @ed25, c.id, @d_kpn, c.name FROM clubs c
WHERE c.name IN (
    'ZAWISZA Bydgoszcz','ŚLĄSK Wrocław','LIDER-AMICUS Lębork','LEGIA Warszawa',
    'HUSARIA Krosno','KALIBER Białystok','GWARDIA Zielona Góra','DRAGON Chełm',
    'PROMIEŃ Bochnia','TKS LOK Tarnów','SPOŁEM Łódź','DZIESIĄTKA Łódź',
    'FLOTA Gdynia','SOKÓŁ Zduńska Wola','BURSZTYN Kalisz','ORNECKIE SMOKI Orneta',
    'PETARDA Kraków'
);

-- ------- WYNIKI -------
SET @r25_1 := (SELECT id FROM rounds WHERE edition_id = @ed25 AND code = 'ZLOT');
SET @r25_2 := (SELECT id FROM rounds WHERE edition_id = @ed25 AND code = 'PB');
SET @r25_3 := (SELECT id FROM rounds WHERE edition_id = @ed25 AND code = 'PP_PZSS');
SET @r25_4 := (SELECT id FROM rounds WHERE edition_id = @ed25 AND code = 'ZM_ZK');

-- PISTOLET 2025
INSERT INTO team_scores (team_id, round_id, score) VALUES
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='KALIBER Białystok'),  @r25_1, 1044),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='KALIBER Białystok'),  @r25_2, 1069),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='KALIBER Białystok'),  @r25_3, 1051),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='KALIBER Białystok'),  @r25_4, 1093),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='DELFIN Tarnów'),       @r25_1, 1073),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='DELFIN Tarnów'),       @r25_3, 1087),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ŚLĄSK Wrocław'),       @r25_1,  734),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ŚLĄSK Wrocław'),       @r25_3, 1067),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ŚLĄSK Wrocław'),       @r25_4, 1086),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ZAWISZA Bydgoszcz'),   @r25_1, 1077),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ZAWISZA Bydgoszcz'),   @r25_2, 1054),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ZAWISZA Bydgoszcz'),   @r25_3, 1079),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ZAWISZA Bydgoszcz'),   @r25_4, 1072),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='COVER Warszawa'),      @r25_1, 1066),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='COVER Warszawa'),      @r25_3, 1078),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='COVER Warszawa'),      @r25_4, 1070),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='PETARDA Kraków'),      @r25_1, 1058),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='PETARDA Kraków'),      @r25_3, 1073),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='PETARDA Kraków'),      @r25_4, 1076),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='GWARDIA Zielona Góra'),@r25_1, 1066),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='GWARDIA Zielona Góra'),@r25_2, 1021),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='GWARDIA Zielona Góra'),@r25_3, 1063),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='GWARDIA Zielona Góra'),@r25_4, 1072),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ZKS Warszawa'),        @r25_1, 1020),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ZKS Warszawa'),        @r25_2, 1070),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ZKS Warszawa'),        @r25_3, 1060),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='ZKS Warszawa'),        @r25_4, 1034),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='FLOTA Gdynia'),        @r25_1, 1061),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='FLOTA Gdynia'),        @r25_2, 1069),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='FLOTA Gdynia'),        @r25_4, 1048),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='SOKÓŁ Zduńska Wola'),  @r25_1, 1064),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='SOKÓŁ Zduńska Wola'),  @r25_3, 1060),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='GROT Puck'),           @r25_1, 1045),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='GROT Puck'),           @r25_2, 1061),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='TARCZA Goleniów'),     @r25_2, 1056),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='AGAT Złotoryja'),      @r25_1,  975),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='AGAT Złotoryja'),      @r25_3, 1042),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='LIDER-AMICUS Lębork'), @r25_1, 1037),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='LIDER-AMICUS Lębork'), @r25_3, 1024),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='LIDER-AMICUS Lębork'), @r25_4, 1012),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='LEGIA Warszawa'),      @r25_1,  979),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='LEGIA Warszawa'),      @r25_3, 1010),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='LEGIA Warszawa'),      @r25_4, 1008),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='CZAK Świdnica'),       @r25_1,  698),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_ppn AND display_name='PRECYZJA Kraków'),     @r25_1,  677)
ON DUPLICATE KEY UPDATE score = VALUES(score);

-- KARABIN 2025
INSERT INTO team_scores (team_id, round_id, score) VALUES
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='ZAWISZA Bydgoszcz'),    @r25_1, 1228.5),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='ZAWISZA Bydgoszcz'),    @r25_2, 1231.1),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='ZAWISZA Bydgoszcz'),    @r25_3, 1243.3),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='ZAWISZA Bydgoszcz'),    @r25_4, 1246.2),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='ŚLĄSK Wrocław'),        @r25_1, 1215.6),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='ŚLĄSK Wrocław'),        @r25_2, 1215.8),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='ŚLĄSK Wrocław'),        @r25_3, 1231.1),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='ŚLĄSK Wrocław'),        @r25_4, 1224.8),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='LIDER-AMICUS Lębork'),  @r25_1, 1210.7),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='LIDER-AMICUS Lębork'),  @r25_2, 1223.6),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='LIDER-AMICUS Lębork'),  @r25_3, 1231.0),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='LIDER-AMICUS Lębork'),  @r25_4, 1223.9),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='LEGIA Warszawa'),       @r25_1, 1227.7),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='LEGIA Warszawa'),       @r25_3, 1215.1),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='LEGIA Warszawa'),       @r25_4, 1218.9),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='HUSARIA Krosno'),       @r25_1,  800.6),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='HUSARIA Krosno'),       @r25_3, 1207.9),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='HUSARIA Krosno'),       @r25_4, 1220.8),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='KALIBER Białystok'),    @r25_1, 1216.3),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='KALIBER Białystok'),    @r25_2, 1206.9),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='KALIBER Białystok'),    @r25_3, 1213.8),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='KALIBER Białystok'),    @r25_4, 1205.9),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='GWARDIA Zielona Góra'), @r25_1,  814.8),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='GWARDIA Zielona Góra'), @r25_2, 1208.0),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='GWARDIA Zielona Góra'), @r25_3, 1213.6),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='DRAGON Chełm'),         @r25_1, 1200.8),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='DRAGON Chełm'),         @r25_3, 1210.1),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='PROMIEŃ Bochnia'),      @r25_1, 1208.1),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='TKS LOK Tarnów'),       @r25_1, 1202.6),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='TKS LOK Tarnów'),       @r25_4, 1205.7),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='SPOŁEM Łódź'),          @r25_1, 1191.5),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='SPOŁEM Łódź'),          @r25_2, 1185.9),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='SPOŁEM Łódź'),          @r25_3, 1202.4),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='SPOŁEM Łódź'),          @r25_4, 1196.5),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='DZIESIĄTKA Łódź'),      @r25_1, 1151.7),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='DZIESIĄTKA Łódź'),      @r25_2, 1176.1),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='DZIESIĄTKA Łódź'),      @r25_3, 1170.8),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='DZIESIĄTKA Łódź'),      @r25_4, 1163.8),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='FLOTA Gdynia'),         @r25_4, 1159.9),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='SOKÓŁ Zduńska Wola'),   @r25_1, 1159.7),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='BURSZTYN Kalisz'),      @r25_3, 1154.0),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='BURSZTYN Kalisz'),      @r25_4, 1140.9),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='ORNECKIE SMOKI Orneta'),@r25_2, 1019.3),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='PETARDA Kraków'),       @r25_1,  735.5),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='PETARDA Kraków'),       @r25_3,  780.5),
 ((SELECT id FROM teams WHERE edition_id=@ed25 AND discipline_id=@d_kpn AND display_name='PETARDA Kraków'),       @r25_4,  404.0)
ON DUPLICATE KEY UPDATE score = VALUES(score);
