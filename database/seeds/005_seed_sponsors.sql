-- 005 — Przykładowi sponsorzy/partnerzy + szkielet galerii i live

SET @ed26 := (SELECT id FROM editions WHERE year = 2026);
SET @rfinal := (SELECT id FROM rounds WHERE edition_id = @ed26 AND code = 'FINAL');

INSERT IGNORE INTO sponsors (edition_id, name, tier, scope, logo, url, description, sort) VALUES
 (@ed26, 'Polski Związek Strzelectwa Sportowego', 'patronat', 'liga',  '/assets/img/logo-pzss.svg',         'https://www.pzss.org.pl',                'Organizator Ligi Młodzieżowej.',                            10),
 (@ed26, 'Ministerstwo Sportu i Turystyki',       'patronat', 'liga',  null,                                'https://www.gov.pl/sport',               'Patronat instytucjonalny — wsparcie sportu młodzieżowego.', 20),
 (@ed26, 'Strzelecki Puchar Gdyni',               'partner',  'final', '/assets/img/logo-puchar-gdyni.svg', '#',                                       'Współgospodarz Finału edycji 2026.',                        30),
 (@ed26, 'Miasto Gdynia',                          'partner',  'spg',   null,                                'https://www.gdynia.pl',                  'Partner Strzeleckiego Pucharu Gdyni.',                      40);

-- Placeholdery galerii (image_url to przyszłe ścieżki w /assets/img/gallery/)
INSERT IGNORE INTO gallery_items (edition_id, round_id, title, image_url, thumb_url, caption, sort) VALUES
 (@ed26, NULL, 'Inauguracja sezonu 2026', '/assets/img/gallery/2026-inauguracja.jpg', '/assets/img/gallery/2026-inauguracja-thumb.jpg', 'Otwarcie sezonu Ligi Młodzieżowej PZSS 2026', 10);

-- Live stream — szkielet (URL ustawiony jako placeholder, do aktualizacji w panelu)
INSERT IGNORE INTO live_streams (edition_id, round_id, title, platform, embed_url, status, sort) VALUES
 (@ed26, @rfinal, 'Finał Ligi Młodzieżowej PZSS 2026 — mecze medalowe', 'youtube', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'upcoming', 10);
