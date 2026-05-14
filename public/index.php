<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/Core/Bootstrap.php';

use App\Core\Bootstrap;
use App\Core\Database;
use App\Core\Router;
use App\Core\View;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Session;
use App\Repository\EditionRepository;
use App\Repository\RoundRepository;
use App\Repository\ContentRepository;
use App\Repository\ResultRepository;
use App\Repository\AdminRepository;
use App\Repository\ProgramRepository;

$config = Bootstrap::init();

try {
    $pdo = Database::pdo($config['db']);
    $hasDb = true;
} catch (\Throwable $e) {
    $pdo = null;
    $hasDb = false;
}

$editionRepo = $hasDb ? new EditionRepository($pdo) : null;
$roundRepo   = $hasDb ? new RoundRepository($pdo) : null;
$contentRepo = $hasDb ? new ContentRepository($pdo) : null;
$resultRepo  = $hasDb ? new ResultRepository($pdo) : null;
$adminRepo   = $hasDb ? new AdminRepository($pdo) : null;
$programRepo = $hasDb ? new ProgramRepository($pdo) : null;

$auth = new Auth(
    $config['admin']['password_hash'] ?? '',
    $config['admin']['session_name'] ?? 'liga_admin'
);

$edition = $editionRepo?->active();
if (!$edition) {
    $edition = [
        'id' => 0,
        'year' => $config['app']['edition_year'],
        'slug' => 'liga-mlodziezowa-2026',
        'title' => $config['app']['name'],
        'subtitle' => 'Ogólnopolski cykl zawodów zespołowych do lat 17',
        'description' => 'Strona w przygotowaniu. Skonfiguruj bazę danych zgodnie z README.',
        'organizer' => 'Polski Związek Strzelectwa Sportowego',
        'pzss_url' => $config['external']['pzss_page'],
        'results_pdf' => $config['external']['results_pdf'],
        'regulation_pdf' => $config['external']['regulamin_pdf'],
        'state_as_of' => null,
    ];
}

View::share('config',  $config);
View::share('edition', $edition);
View::share('hasDb',   $hasDb);
View::share('partners', $hasDb ? $contentRepo->partners() : []);
View::share('isAdmin', $auth->check());

$router = new Router();

/* ---------------- Public pages ---------------- */
$router->get('/', static function () use ($edition, $hasDb, $roundRepo, $contentRepo) {
    $rounds  = $hasDb ? $roundRepo->forEdition((int)$edition['id']) : [];
    $next    = $hasDb ? $roundRepo->nextUpcoming((int)$edition['id']) : null;
    $news    = $hasDb ? $contentRepo->latestNews((int)$edition['id'], 3) : [];
    $docs    = $hasDb ? $contentRepo->documents((int)$edition['id']) : [];
    return View::render('pages/home', [
        'title' => $edition['title'], 'rounds' => $rounds, 'next' => $next, 'news' => $news, 'docs' => $docs,
    ]);
});

$router->get('/wyniki', static function () use ($edition, $hasDb, $resultRepo) {
    $disciplines = $hasDb ? $resultRepo->disciplines() : [];
    $standings = [];
    if ($hasDb) {
        foreach ($disciplines as $d) {
            $standings[$d['code']] = [
                'name'  => $d['name'], 'short' => $d['short'], 'icon' => $d['icon'] ?? null,
                'rows'  => $resultRepo->standings((int)$edition['id'], (int)$d['id']),
            ];
        }
    }
    return View::render('pages/wyniki', [
        'title' => 'Wyniki — ' . $edition['title'], 'disciplines' => $disciplines, 'standings' => $standings,
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

$router->get('/final-puchar-gdyni', static function () use ($edition, $hasDb, $roundRepo, $programRepo) {
    $rounds = $hasDb ? $roundRepo->forEdition((int)$edition['id']) : [];
    $final  = null;
    foreach ($rounds as $r) { if ((int)$r['is_final'] === 1) { $final = $r; break; } }
    $program = ($hasDb && $final) ? $programRepo->eventsForRound((int)$final['id']) : [];
    $venue   = $hasDb ? $programRepo->venueBySlug('strzelnica-gdynia') : null;
    return View::render('pages/final', [
        'title' => 'Finał Ligi Młodzieżowej & Strzelecki Puchar Gdyni',
        'final' => $final, 'program' => $program, 'venue' => $venue,
    ]);
});

$router->get('/spg', static function () use ($hasDb, $programRepo) {
    $venue = $hasDb ? $programRepo->venueBySlug('strzelnica-gdynia') : null;
    return View::render('pages/spg', ['title' => 'Strzelecki Puchar Gdyni', 'venue' => $venue]);
});

$router->get('/aktualnosci', static function () use ($edition, $hasDb, $contentRepo) {
    return View::render('pages/news_list', [
        'title' => 'Aktualności — ' . $edition['title'],
        'news'  => $hasDb ? $contentRepo->latestNews((int)$edition['id'], 30) : [],
    ]);
});

$router->get('/aktualnosci/{slug}', static function (array $p) use ($hasDb, $contentRepo) {
    $post = $hasDb ? $contentRepo->newsBySlug((string)$p['slug']) : null;
    if (!$post) { http_response_code(404); return View::render('pages/404', ['title' => 'Nie znaleziono']); }
    return View::render('pages/news_single', ['title' => $post['title'], 'post' => $post]);
});

$router->get('/kontakt', static function () { return View::render('pages/kontakt', ['title' => 'Kontakt']); });

/* ---------------- Admin ---------------- */
$adminLayout = static function (string $tpl, array $data = []): string {
    return View::render($tpl, $data, 'admin/layout');
};

$router->get('/admin/login', static function () use ($auth, $adminLayout) {
    if ($auth->check()) { header('Location: /admin'); exit; }
    return $adminLayout('admin/login', ['title' => 'Logowanie · Panel administratora', 'flashes' => Flash::pull()]);
});

$router->post('/admin/login', static function () use ($auth) {
    Csrf::requireValid();
    $password = (string)($_POST['password'] ?? '');
    if ($auth->attempt($password)) { Flash::add('ok', 'Zalogowano.'); header('Location: /admin'); exit; }
    Flash::add('err', 'Niepoprawne hasło lub brak konfiguracji hasła administratora.');
    header('Location: /admin/login'); exit;
});

$router->post('/admin/logout', static function () use ($auth) {
    Csrf::requireValid(); $auth->logout(); header('Location: /admin/login'); exit;
});

$router->get('/admin', static function () use ($auth, $edition, $hasDb, $adminRepo, $adminLayout) {
    $auth->requireLogin();
    $stats = $hasDb ? $adminRepo->stats((int)$edition['id']) : ['teams_total'=>0,'scores_total'=>0,'rounds_done'=>0,'news_total'=>0];
    return $adminLayout('admin/dashboard', ['title' => 'Panel administratora', 'stats' => $stats, 'flashes' => Flash::pull()]);
});

/* News */
$router->get('/admin/news', static function () use ($auth, $hasDb, $adminRepo, $adminLayout) {
    $auth->requireLogin();
    return $adminLayout('admin/news_list', [
        'title' => 'Aktualności · Panel',
        'rows' => $hasDb ? $adminRepo->listNews() : [],
        'flashes' => Flash::pull(),
    ]);
});

$router->get('/admin/news/new', static function () use ($auth, $adminLayout) {
    $auth->requireLogin();
    return $adminLayout('admin/news_form', ['title' => 'Nowy wpis', 'post' => null, 'flashes' => Flash::pull()]);
});

$router->get('/admin/news/{id}', static function (array $p) use ($auth, $hasDb, $adminRepo, $adminLayout) {
    $auth->requireLogin();
    $post = $hasDb ? $adminRepo->findNews((int)$p['id']) : null;
    if (!$post) { http_response_code(404); return $adminLayout('admin/news_form', ['title'=>'Brak wpisu','post'=>null,'flashes'=>Flash::pull()]); }
    return $adminLayout('admin/news_form', ['title' => 'Edycja: ' . $post['title'], 'post' => $post, 'flashes' => Flash::pull()]);
});

$router->post('/admin/news', static function () use ($auth, $hasDb, $adminRepo, $edition) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { Flash::add('err','Brak bazy.'); header('Location: /admin/news'); exit; }
    $id    = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $data  = [
        'edition_id' => (int)$edition['id'] ?: null,
        'title' => trim($_POST['title'] ?? ''),
        'slug'  => trim($_POST['slug']  ?? ''),
        'lead'  => trim($_POST['lead']  ?? ''),
        'body'  => $_POST['body']  ?? '',
        'is_pinned'    => !empty($_POST['is_pinned']),
        'published_at' => $_POST['published_at'] ?: date('Y-m-d H:i:s'),
    ];
    if ($data['title'] === '' || $data['slug'] === '') {
        Flash::add('err','Tytuł i slug są wymagane.'); header('Location: /admin/news/new'); exit;
    }
    try {
        $newId = $adminRepo->saveNews($id, $data);
        Flash::add('ok','Zapisano wpis #'.$newId.'.');
        header('Location: /admin/news'); exit;
    } catch (\Throwable $e) {
        Flash::add('err','Błąd zapisu: '.$e->getMessage()); header('Location: /admin/news'); exit;
    }
});

$router->post('/admin/news/{id}/delete', static function (array $p) use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if ($hasDb) { $adminRepo->deleteNews((int)$p['id']); Flash::add('ok','Wpis usunięty.'); }
    header('Location: /admin/news'); exit;
});

/* Rounds + wyniki */
$router->get('/admin/rundy', static function () use ($auth, $hasDb, $roundRepo, $edition, $adminLayout) {
    $auth->requireLogin();
    return $adminLayout('admin/rounds', [
        'title' => 'Rundy · Panel',
        'rounds' => $hasDb ? $roundRepo->forEdition((int)$edition['id']) : [],
        'flashes' => Flash::pull(),
    ]);
});

$router->post('/admin/rundy/{id}/status', static function (array $p) use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    $status = (string)($_POST['status'] ?? 'planned');
    if (!in_array($status, ['planned','ongoing','finished'], true)) { $status = 'planned'; }
    if ($hasDb) { $adminRepo->setRoundStatus((int)$p['id'], $status); Flash::add('ok','Status rundy zaktualizowany.'); }
    header('Location: /admin/rundy'); exit;
});

$router->get('/admin/rundy/{id}/wyniki', static function (array $p) use ($auth, $hasDb, $adminRepo, $resultRepo, $edition, $adminLayout) {
    $auth->requireLogin();
    if (!$hasDb) { return $adminLayout('admin/round_scores', ['title'=>'Wyniki','round'=>null,'teams'=>[],'scores'=>[],'disciplines'=>[],'flashes'=>Flash::pull()]); }
    $round = $adminRepo->findRound((int)$p['id']);
    if (!$round) { http_response_code(404); return $adminLayout('admin/round_scores', ['title'=>'Brak rundy','round'=>null,'teams'=>[],'scores'=>[],'disciplines'=>[],'flashes'=>Flash::pull()]); }
    $disciplines = $resultRepo->disciplines();
    $teams = [];
    foreach ($disciplines as $d) {
        $teams[$d['code']] = $adminRepo->teamsForDiscipline((int)$edition['id'], (int)$d['id']);
    }
    return $adminLayout('admin/round_scores', [
        'title' => 'Wyniki: ' . $round['short_label'],
        'round' => $round, 'disciplines' => $disciplines, 'teams' => $teams,
        'scores' => $adminRepo->scoresForRound((int)$round['id']),
        'flashes' => Flash::pull(),
    ]);
});

$router->post('/admin/rundy/{id}/wyniki', static function (array $p) use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { header('Location: /admin/rundy'); exit; }
    $roundId = (int)$p['id'];
    $added = 0;
    foreach (($_POST['score'] ?? []) as $teamId => $val) {
        $val = str_replace(',', '.', (string)$val);
        if ($val === '' || !is_numeric($val)) continue;
        $adminRepo->upsertScore((int)$teamId, $roundId, (float)$val);
        $added++;
    }
    Flash::add('ok', "Zapisano wyników: $added.");
    header('Location: /admin/rundy/' . $roundId . '/wyniki'); exit;
});

$router->post('/admin/wynik/{id}/delete', static function (array $p) use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if ($hasDb) {
        $back = (int)($_POST['round_id'] ?? 0);
        $adminRepo->deleteScore((int)$p['id']);
        Flash::add('ok','Wynik usunięty.');
        header('Location: /admin/rundy/' . $back . '/wyniki'); exit;
    }
    header('Location: /admin/rundy'); exit;
});

/* Import CSV */
$router->get('/admin/import', static function () use ($auth, $hasDb, $roundRepo, $edition, $adminLayout) {
    $auth->requireLogin();
    return $adminLayout('admin/import', [
        'title'  => 'Import wyników CSV',
        'rounds' => $hasDb ? $roundRepo->forEdition((int)$edition['id']) : [],
        'flashes' => Flash::pull(),
    ]);
});

$router->post('/admin/import', static function () use ($auth, $hasDb, $adminRepo, $resultRepo, $edition) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { Flash::add('err','Brak bazy.'); header('Location: /admin/import'); exit; }
    $roundId   = (int)($_POST['round_id'] ?? 0);
    $disciplineCode = (string)($_POST['discipline'] ?? '');
    $file = $_FILES['csv'] ?? null;
    if (!$file || ($file['error'] ?? 1) !== UPLOAD_ERR_OK) {
        Flash::add('err','Brak pliku CSV lub błąd uploadu.'); header('Location: /admin/import'); exit;
    }
    $discipline = null;
    foreach ($resultRepo->disciplines() as $d) { if ($d['code'] === $disciplineCode) { $discipline = $d; break; } }
    if (!$discipline) { Flash::add('err','Nieznana dyscyplina.'); header('Location: /admin/import'); exit; }

    // Mapowanie nazw klubów -> team_id
    $teamMap = [];
    foreach ($adminRepo->teamsForDiscipline((int)$edition['id'], (int)$discipline['id']) as $t) {
        $teamMap[mb_strtolower(trim($t['display_name']))] = (int)$t['id'];
    }

    $h = fopen($file['tmp_name'], 'rb');
    if (!$h) { Flash::add('err','Nie mogę otworzyć pliku.'); header('Location: /admin/import'); exit; }
    $rows = 0; $ok = 0; $skipped = [];
    while (($r = fgetcsv($h, 0, ',', '"', '\\')) !== false) {
        $rows++;
        if ($rows === 1 && !is_numeric(str_replace(',', '.', (string)$r[1] ?? ''))) { continue; }
        $name = isset($r[0]) ? mb_strtolower(trim((string)$r[0])) : '';
        $score = isset($r[1]) ? str_replace(',', '.', trim((string)$r[1])) : '';
        if ($name === '' || !is_numeric($score)) { $skipped[] = $r[0] ?? '(pusty)'; continue; }
        $tid = $teamMap[$name] ?? null;
        if (!$tid) { $skipped[] = $r[0] ?? $name; continue; }
        $adminRepo->upsertScore($tid, $roundId, (float)$score);
        $ok++;
    }
    fclose($h);
    Flash::add('ok', "Zaimportowano: $ok. Pominięto: " . count($skipped) . (count($skipped) ? ' (' . implode(', ', array_slice($skipped, 0, 5)) . (count($skipped)>5?'…':'') . ')' : '.'));
    header('Location: /admin/import'); exit;
});

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
