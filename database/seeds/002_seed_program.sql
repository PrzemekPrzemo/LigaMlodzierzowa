-- Program Finału Ligi Młodzieżowej PZSS 2026 + Strzelecki Puchar Gdyni

INSERT INTO venues (slug, name, address, city, map_url, description) VALUES
 ('strzelnica-gdynia',
  'Strzelnica klubu sportowego — Gdynia',
  'ul. Strzelecka 1',
  'Gdynia',
  'https://maps.google.com/?q=Strzelnica+Gdynia',
  'Min. 26 stanowisk z tarczami elektronicznymi, ekrany prezentacji wyników, nagłośnienie — zgodnie z wymaganiami regulaminu PZSS dla Finału Ligi.')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description);

SET @ed := (SELECT id FROM editions WHERE year = 2026);
SET @rf := (SELECT id FROM rounds WHERE edition_id = @ed AND code = 'FINAL');

INSERT INTO round_events (round_id, day_no, day_label, time_start, time_end, title, kind, location, sort) VALUES
 (@rf, 1, 'Dzień 1 — kwalifikacje',  '09:00','09:30','Rejestracja zespołów',                'rejestracja',  'Biuro zawodów',     10),
 (@rf, 1, 'Dzień 1 — kwalifikacje',  '10:00','10:30','Strzały próbne (10 min) + przygotowanie','treningi',     'Strzelnica',         20),
 (@rf, 1, 'Dzień 1 — kwalifikacje',  '10:30','13:00','Mecze grupowe G1 (karabin / pistolet)', 'mecze',        'Strzelnica',         30),
 (@rf, 1, 'Dzień 1 — kwalifikacje',  '14:00','16:30','Mecze grupowe G2 (karabin / pistolet)', 'mecze',        'Strzelnica',         40),
 (@rf, 2, 'Dzień 2 — kwalifikacje',  '10:00','13:00','Mecze grupowe — runda 2',              'mecze',        'Strzelnica',         50),
 (@rf, 2, 'Dzień 2 — kwalifikacje',  '14:00','16:30','Mecze grupowe — runda 3',              'mecze',        'Strzelnica',         60),
 (@rf, 2, 'Dzień 2 — kwalifikacje',  '17:00','17:30','Ogłoszenie par meczów medalowych',     'organizacyjne','Strzelnica',         70),
 (@rf, 3, 'Dzień 3 — mecze medalowe','09:30','10:00','Sprawdzenie obecności (na 30 min przed finałem)','organizacyjne','Biuro zawodów', 10),
 (@rf, 3, 'Dzień 3 — mecze medalowe','10:00','10:13','Prezentacja par finałowych',           'organizacyjne','Strzelnica',         20),
 (@rf, 3, 'Dzień 3 — mecze medalowe','10:13','10:23','Strzały próbne (10 min)',              'treningi',     'Strzelnica',         30),
 (@rf, 3, 'Dzień 3 — mecze medalowe','10:30','11:30','Mecz o brązowy medal (karabin)',       'mecze',        'Strzelnica',         40),
 (@rf, 3, 'Dzień 3 — mecze medalowe','11:45','12:45','Mecz o złoty medal (karabin)',         'mecze',        'Strzelnica',         50),
 (@rf, 3, 'Dzień 3 — mecze medalowe','13:30','14:30','Mecz o brązowy medal (pistolet)',      'mecze',        'Strzelnica',         60),
 (@rf, 3, 'Dzień 3 — mecze medalowe','14:45','15:45','Mecz o złoty medal (pistolet)',        'mecze',        'Strzelnica',         70),
 (@rf, 3, 'Dzień 3 — mecze medalowe','16:30','17:30','Ceremonia dekoracji i zakończenie',    'ceremonia',    'Strzelnica',         80);
