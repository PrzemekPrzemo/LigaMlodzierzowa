<?php
declare(strict_types=1);

return [
    'app' => [
        'name' => 'Liga Młodzieżowa PZSS 2026',
        'short_name' => 'Liga Młodzieżowa 2026',
        'edition_year' => 2026,
        'base_url' => 'https://liga.example.pl',
        'timezone' => 'Europe/Warsaw',
        'locale' => 'pl_PL.UTF-8',
        'debug' => false,
        'cache_ttl' => 600,
    ],
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'liga_mlodziezowa',
        'user' => 'liga_user',
        'pass' => 'CHANGE_ME',
        'charset' => 'utf8mb4',
    ],
    'external' => [
        'pzss_page'        => 'https://www.pzss.org.pl/szkolenie/liga-mlodziezowa/liga-mlodziezowa-2026',
        'results_pdf'      => 'https://www.pzss.org.pl/assets/files/liga-mlodziezowa-pzss-2026.pdf',
        'regulamin_pdf'    => 'https://www.pzss.org.pl/assets/files/zawody-i-organizacja/regulamin-final-ligi-mlodziezowej-2026.pdf',
        'pzss_home'        => 'https://www.pzss.org.pl',
    ],
    'admin' => [
        // Wygeneruj: php -r "echo password_hash('twojeHaslo', PASSWORD_DEFAULT).PHP_EOL;"
        'password_hash' => '$2y$12$REPLACE_WITH_OWN_HASH',
        'session_name' => 'liga_admin',
    ],
];
