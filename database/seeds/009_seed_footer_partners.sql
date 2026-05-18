-- 009 — Domyślni "patroni / partnerzy" do footera + paska (idempotentne)

SET @ed26 := (SELECT id FROM editions WHERE year = 2026);

-- INSERT z UPDATE: jeśli wpis o tej samej nazwie istnieje, aktualizuje kluczowe pola.
INSERT INTO sponsors (edition_id, name, tier, category, scope, logo, url, description, sort, is_visible) VALUES
 (@ed26, 'Polski Związek Strzelectwa Sportowego', 'patronat', 'patronat_honorowy', 'wszystko',
        '/assets/img/logo-pzss.svg', 'https://www.pzss.org.pl',
        'Organizator Ligi Młodzieżowej.', 10, 1),
 (@ed26, 'Liga Młodzieżowa PZSS', 'partner', 'partner', 'liga',
        '/assets/img/logo-liga.svg', 'https://www.pzss.org.pl/szkolenie/liga-mlodziezowa/liga-mlodziezowa-2026',
        'Cykl zawodów strzeleckich do lat 17.', 20, 1),
 (@ed26, 'Strzelecki Puchar Gdyni', 'partner', 'partner', 'final',
        '/assets/img/logo-puchar-gdyni.svg', '#',
        'Współgospodarz Finału edycji 2026.', 30, 1),
 (@ed26, 'Miasto Gdynia', 'partner', 'partner', 'spg',
        NULL, 'https://www.gdynia.pl',
        'Partner Strzeleckiego Pucharu Gdyni.', 40, 1),
 (@ed26, 'Ministerstwo Sportu i Turystyki', 'patronat', 'patronat_honorowy', 'liga',
        NULL, 'https://www.gov.pl/sport',
        'Patronat instytucjonalny.', 15, 1)
ON DUPLICATE KEY UPDATE
    tier       = VALUES(tier),
    category   = VALUES(category),
    logo       = COALESCE(sponsors.logo, VALUES(logo)),
    url        = COALESCE(sponsors.url,  VALUES(url)),
    is_visible = 1;
