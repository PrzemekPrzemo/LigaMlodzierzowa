<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/Core/Bootstrap.php';

use App\Core\Bootstrap;
use App\Core\Database;
use App\Core\Router;
use App\Core\View;
use App\Repository\EditionRepository;
use App\Repository\RoundRepository;
use App\Repository\ContentRepository;
use App\Repository\ResultRepository;

$config = Bootstrap::init();

try {
    $pdo = Database::pdo($config['db']);
    $hasDb = true;
} catch (\Throwable $e) {
    $pdo = null;
    $hasDb = false;
    $dbError = $e->getMessage();
}

$editionRepo = $hasDb ? new EditionRepository($pdo) : null;
$roundRepo   = $hasDb ? new RoundRepository($pdo) : null;
$contentRepo = $hasDb ? new ContentRepository($pdo) : null;
$resultRepo  = $hasDb ? new ResultRepository($pdo) : null;

$edition = $editionRepo?->active();
if (!$edition) {
    $edition = [
        'id' => 0,
        'year' => $config['app']['edition_year'],
        'slug' => 'liga-mlodziezowa-2026',
        'title' => $config['app']['name'],
        'subtitle' => 'Ogólnopolski cykl zawodów strzeleckich dla młodzieży',
        'description' => 'Strona w przygotowaniu. Skonfiguruj bazę danych zgodnie z README.',
        'organizer' => 'Polski Związek Strzelectwa Sportowego',
        'pzss_url' => $config['external']['pzss_page'],
        'results_pdf' => $config['external']['results_pdf'],
        'regulation_pdf' => $config['external']['regulamin_pdf'],
    ];
}

View::share('config',  $config);
View::share('edition', $edition);
View::share('hasDb',   $hasDb);
View::share('partners', $hasDb ? $contentRepo->partners() : []);

$router = new Router();

$router->get('/', static function () use ($edition, $hasDb, $roundRepo, $contentRepo) {
    $rounds  = $hasDb ? $roundRepo->forEdition((int)$edition['id']) : [];
    $next    = $hasDb ? $roundRepo->nextUpcoming((int)$edition['id']) : null;
    $news    = $hasDb ? $contentRepo->latestNews((int)$edition['id'], 3) : [];
    $docs    = $hasDb ? $contentRepo->documents((int)$edition['id']) : [];
    return View::render('pages/home', [
        'title'  => $edition['title'],
        'rounds' => $rounds,
        'next'   => $next,
        'news'   => $news,
        'docs'   => $docs,
    ]);
});

$router->get('/wyniki', static function () use ($edition, $hasDb, $resultRepo) {
    $disciplines = $hasDb ? $resultRepo->disciplines() : [];
    $standings = [];
    if ($hasDb) {
        foreach ($disciplines as $d) {
            $standings[$d['code']] = [
                'name'    => $d['name'],
                'short'   => $d['short'],
                'icon'    => $d['icon'] ?? null,
                'rows'    => $resultRepo->standings((int)$edition['id'], (int)$d['id']),
            ];
        }
    }
    return View::render('pages/wyniki', [
        'title'       => 'Wyniki — ' . $edition['title'],
        'disciplines' => $disciplines,
        'standings'   => $standings,
    ]);
});

$router->get('/terminarz', static function () use ($edition, $hasDb, $roundRepo) {
    return View::render('pages/terminarz', [
        'title'  => 'Terminarz — ' . $edition['title'],
        'rounds' => $hasDb ? $roundRepo->forEdition((int)$edition['id']) : [],
    ]);
});

$router->get('/regulamin', static function () use ($edition, $hasDb, $contentRepo) {
    return View::render('pages/regulamin', [
        'title' => 'Regulamin — ' . $edition['title'],
        'docs'  => $hasDb ? $contentRepo->documents((int)$edition['id'], 'regulamin') : [],
    ]);
});

$router->get('/final-puchar-gdyni', static function () use ($edition, $hasDb, $roundRepo) {
    $rounds = $hasDb ? $roundRepo->forEdition((int)$edition['id']) : [];
    $final  = null;
    foreach ($rounds as $r) {
        if ((int)$r['is_final'] === 1) { $final = $r; break; }
    }
    return View::render('pages/final', [
        'title' => 'Finał Ligi Młodzieżowej & Strzelecki Puchar Gdyni',
        'final' => $final,
    ]);
});

$router->get('/aktualnosci',          static function () use ($edition, $hasDb, $contentRepo) {
    return View::render('pages/news_list', [
        'title' => 'Aktualności — ' . $edition['title'],
        'news'  => $hasDb ? $contentRepo->latestNews((int)$edition['id'], 30) : [],
    ]);
});

$router->get('/aktualnosci/{slug}',   static function (array $p) use ($hasDb, $contentRepo) {
    $post = $hasDb ? $contentRepo->newsBySlug((string)$p['slug']) : null;
    if (!$post) { http_response_code(404); return View::render('pages/404', ['title' => 'Nie znaleziono']); }
    return View::render('pages/news_single', ['title' => $post['title'], 'post' => $post]);
});

$router->get('/kontakt', static function () {
    return View::render('pages/kontakt', ['title' => 'Kontakt']);
});

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
