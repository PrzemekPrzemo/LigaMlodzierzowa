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
use App\Core\Slugger;
use App\Repository\EditionRepository;
use App\Repository\RoundRepository;
use App\Repository\ContentRepository;
use App\Repository\ResultRepository;
use App\Repository\AdminRepository;
use App\Repository\ProgramRepository;
use App\Repository\ClubRepository;
use App\Repository\AthleteRepository;
use App\Repository\MediaRepository;

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
$clubRepo    = $hasDb ? new ClubRepository($pdo) : null;
$athleteRepo = $hasDb ? new AthleteRepository($pdo) : null;
$mediaRepo   = $hasDb ? new MediaRepository($pdo) : null;

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
// Footer "Organizator i partnerzy" — top sponsorzy z kategorii patronat/sponsor_glowny/partner.
// Wszystko edytowalne w /admin/sponsorzy.
$footerSponsors = [];
if ($hasDb) {
    foreach ($mediaRepo->sponsors((int)$edition['id']) as $sp) {
        if (in_array($sp['category'], ['patronat_honorowy','sponsor_glowny','partner'], true)) {
            $footerSponsors[] = $sp;
        }
        if (count($footerSponsors) >= 6) break;
    }
}
View::share('partners', $footerSponsors);
View::share('marqueeSponsors', $hasDb ? $mediaRepo->forMarquee((int)$edition['id']) : []);
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
    $tables    = [];
    if ($hasDb) {
        foreach ($disciplines as $d) {
            $standings[$d['code']] = [
                'name' => $d['name'], 'short' => $d['short'],
                'rows' => $resultRepo->standings((int)$edition['id'], (int)$d['id']),
            ];
            $tables[$d['code']] = [
                'name' => $d['name'],
                'data' => $resultRepo->roundsTable((int)$edition['id'], (int)$d['id']),
            ];
        }
    }
    return View::render('pages/wyniki', [
        'title' => 'Wyniki — ' . $edition['title'], 'disciplines' => $disciplines,
        'standings' => $standings, 'tables' => $tables,
    ]);
});

/* Kluby */
$router->get('/kluby', static function () use ($edition, $hasDb, $clubRepo) {
    return View::render('pages/clubs', [
        'title' => 'Kluby — ' . $edition['title'],
        'clubs' => $hasDb ? $clubRepo->clubsForEdition((int)$edition['id']) : [],
    ]);
});

$router->get('/klub/{slug}', static function (array $p) use ($edition, $hasDb, $clubRepo, $athleteRepo) {
    if (!$hasDb) { http_response_code(404); return View::render('pages/404', ['title' => 'Brak bazy']); }
    $club = $clubRepo->bySlug((string)$p['slug']);
    if (!$club) { http_response_code(404); return View::render('pages/404', ['title' => 'Klub nie znaleziony']); }
    $athletes = $athleteRepo->forClub((int)$club['id']);
    $scores   = $athleteRepo->scoresForClubInEdition((int)$club['id'], (int)$edition['id']);
    return View::render('pages/club_single', [
        'title' => $club['name'],
        'club'  => $club, 'athletes' => $athletes, 'scores' => $scores,
    ]);
});

/* Archiwum */
$router->get('/archiwum', static function () use ($hasDb, $editionRepo) {
    return View::render('pages/archive', [
        'title'    => 'Archiwum sezonów',
        'editions' => $hasDb ? $editionRepo->archive() : [],
    ]);
});

$router->get('/archiwum/{year}', static function (array $p) use ($hasDb, $editionRepo, $resultRepo) {
    if (!$hasDb) { http_response_code(404); return View::render('pages/404', ['title' => '404']); }
    $ed = $editionRepo->byYear((int)$p['year']);
    if (!$ed) { http_response_code(404); return View::render('pages/404', ['title' => 'Edycja nie znaleziona']); }
    $disciplines = $resultRepo->disciplines();
    $tables = [];
    foreach ($disciplines as $d) {
        $tables[$d['code']] = [
            'name' => $d['name'],
            'data' => $resultRepo->roundsTable((int)$ed['id'], (int)$d['id']),
        ];
    }
    return View::render('pages/archive_edition', [
        'title' => $ed['title'], 'archive_edition' => $ed, 'tables' => $tables,
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

$router->get('/final-puchar-gdyni', static function () use ($edition, $hasDb, $roundRepo, $programRepo, $mediaRepo) {
    $rounds = $hasDb ? $roundRepo->forEdition((int)$edition['id']) : [];
    $final  = null;
    foreach ($rounds as $r) { if ((int)$r['is_final'] === 1) { $final = $r; break; } }
    $program  = ($hasDb && $final) ? $programRepo->eventsForRound((int)$final['id']) : [];
    $venue    = $hasDb ? $programRepo->venueBySlug('strzelnica-gdynia') : null;
    $sponsors = $hasDb ? $mediaRepo->sponsors((int)$edition['id'], 'final') : [];
    $lives    = $hasDb ? $mediaRepo->liveStreams((int)$edition['id']) : [];
    return View::render('pages/final', [
        'title' => 'Finał Ligi Młodzieżowej & Strzelecki Puchar Gdyni',
        'final' => $final, 'program' => $program, 'venue' => $venue,
        'sponsors' => $sponsors, 'lives' => $lives,
    ]);
});

$router->get('/spg', static function () use ($edition, $hasDb, $programRepo, $mediaRepo) {
    $venue    = $hasDb ? $programRepo->venueBySlug('strzelnica-gdynia') : null;
    $sponsors = $hasDb ? $mediaRepo->sponsors((int)$edition['id'], 'spg') : [];
    return View::render('pages/spg', [
        'title' => 'Strzelecki Puchar Gdyni',
        'venue' => $venue, 'sponsors' => $sponsors,
    ]);
});

$router->get('/partnerzy', static function () use ($edition, $hasDb, $mediaRepo) {
    return View::render('pages/partners', [
        'title'  => 'Partnerzy i sponsorzy — ' . $edition['title'],
        'groups' => $hasDb ? $mediaRepo->allGroupedByCategory((int)$edition['id']) : [],
    ]);
});

$router->get('/galeria', static function () use ($edition, $hasDb, $mediaRepo) {
    return View::render('pages/gallery', [
        'title' => 'Galeria — ' . $edition['title'],
        'gallery' => $hasDb ? $mediaRepo->gallery((int)$edition['id']) : [],
    ]);
});

$router->get('/live', static function () use ($edition, $hasDb, $mediaRepo) {
    return View::render('pages/live', [
        'title' => 'Transmisje na żywo — ' . $edition['title'],
        'lives' => $hasDb ? $mediaRepo->liveStreams((int)$edition['id']) : [],
    ]);
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
        'computed' => $adminRepo->teamsWithAthleteScores((int)$round['id']),
        'flashes' => Flash::pull(),
    ]);
});

$router->post('/admin/rundy/{id}/wyniki', static function (array $p) use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { header('Location: /admin/rundy'); exit; }
    $roundId = (int)$p['id'];
    // Zespoły z wynikami zawodników liczone są automatycznie — ignoruj ręczne wpisy
    $autoMap = $adminRepo->teamsWithAthleteScores($roundId);
    $added = 0; $skipped = 0;
    foreach (($_POST['score'] ?? []) as $teamId => $val) {
        $teamId = (int)$teamId;
        if (isset($autoMap[$teamId])) { $skipped++; continue; }
        $val = str_replace(',', '.', (string)$val);
        if ($val === '' || !is_numeric($val)) continue;
        $adminRepo->upsertScore($teamId, $roundId, (float)$val);
        $added++;
    }
    $msg = "Zapisano wyników ręcznych: $added.";
    if ($skipped > 0) { $msg .= " Pominięto $skipped zespołów liczonych automatycznie z zawodników."; }
    Flash::add('ok', $msg);
    header('Location: /admin/rundy/' . $roundId . '/wyniki'); exit;
});

$router->post('/admin/rundy/{id}/przelicz', static function (array $p) use ($auth, $hasDb, $adminRepo, $edition) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { header('Location: /admin/rundy'); exit; }
    $roundId = (int)$p['id'];
    $count = $adminRepo->recomputeAllTeamsInRound($roundId, (int)$edition['id']);
    Flash::add('ok', "Przeliczono z wyników zawodników: $count zespołów. Zespoły bez wyników indywidualnych pozostały bez zmian.");
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

/* ---- CRUD Kluby ---- */
$router->get('/admin/kluby', static function () use ($auth, $hasDb, $adminRepo, $adminLayout) {
    $auth->requireLogin();
    return $adminLayout('admin/clubs_list', [
        'title' => 'Kluby · Panel',
        'rows' => $hasDb ? $adminRepo->listClubs() : [],
        'flashes' => Flash::pull(),
    ]);
});

$router->get('/admin/kluby/new', static function () use ($auth, $adminLayout) {
    $auth->requireLogin();
    return $adminLayout('admin/club_form', [
        'title' => 'Nowy klub', 'club' => null, 'teams' => [],
        'disciplines' => [], 'edition' => null, 'flashes' => Flash::pull(),
    ]);
});

$router->get('/admin/kluby/{id}', static function (array $p) use ($auth, $hasDb, $adminRepo, $resultRepo, $edition, $adminLayout) {
    $auth->requireLogin();
    if (!$hasDb) { http_response_code(404); return $adminLayout('admin/club_form', ['title'=>'Brak bazy','club'=>null,'teams'=>[],'disciplines'=>[],'edition'=>null,'flashes'=>Flash::pull()]); }
    $club = $adminRepo->findClub((int)$p['id']);
    if (!$club) { http_response_code(404); return $adminLayout('admin/club_form', ['title'=>'Brak klubu','club'=>null,'teams'=>[],'disciplines'=>[],'edition'=>null,'flashes'=>Flash::pull()]); }
    return $adminLayout('admin/club_form', [
        'title'       => 'Edycja klubu: ' . $club['name'],
        'club'        => $club,
        'teams'       => $adminRepo->teamsForClub((int)$club['id'], (int)$edition['id']),
        'disciplines' => $resultRepo->disciplines(),
        'edition'     => $edition,
        'athletes'    => $adminRepo->listAthletes((int)$club['id']),
        'flashes'     => Flash::pull(),
    ]);
});

$router->post('/admin/kluby', static function () use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { Flash::add('err','Brak bazy.'); header('Location: /admin/kluby'); exit; }
    $id   = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $name = trim($_POST['name'] ?? '');
    if ($name === '') { Flash::add('err','Nazwa klubu jest wymagana.'); header('Location: /admin/kluby/' . ($id ?? 'new')); exit; }
    $slug = trim($_POST['slug'] ?? '') ?: Slugger::make($name);
    $data = [
        'name'    => $name,
        'short'   => trim($_POST['short']   ?? ''),
        'slug'    => $slug,
        'city'    => trim($_POST['city']    ?? ''),
        'region'  => trim($_POST['region']  ?? ''),
        'logo'    => trim($_POST['logo']    ?? ''),
        'website' => trim($_POST['website'] ?? ''),
    ];
    try {
        $newId = $adminRepo->saveClub($id, $data);
        Flash::add('ok','Zapisano klub.');
        header('Location: /admin/kluby/' . $newId); exit;
    } catch (\Throwable $e) {
        Flash::add('err','Błąd zapisu: '.$e->getMessage());
        header('Location: /admin/kluby' . ($id ? '/' . $id : '/new')); exit;
    }
});

$router->post('/admin/kluby/{id}/delete', static function (array $p) use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if ($hasDb) { $adminRepo->deleteClub((int)$p['id']); Flash::add('ok','Klub usunięty.'); }
    header('Location: /admin/kluby'); exit;
});

$router->post('/admin/kluby/{id}/zespol', static function (array $p) use ($auth, $hasDb, $adminRepo, $edition) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { header('Location: /admin/kluby'); exit; }
    $clubId = (int)$p['id'];
    $club = $adminRepo->findClub($clubId);
    if ($club) {
        $disciplineId = (int)($_POST['discipline_id'] ?? 0);
        $action = $_POST['action'] ?? 'add';
        if ($action === 'remove') {
            $teamId = (int)($_POST['team_id'] ?? 0);
            if ($teamId) { $adminRepo->deleteTeam($teamId); Flash::add('ok','Zespół usunięty z edycji.'); }
        } elseif ($disciplineId) {
            $adminRepo->createTeam((int)$edition['id'], $clubId, $disciplineId, $club['name']);
            Flash::add('ok','Zespół dodany do edycji.');
        }
    }
    header('Location: /admin/kluby/' . $clubId); exit;
});

/* ---- CRUD Zawodnicy ---- */
$router->get('/admin/zawodnicy', static function () use ($auth, $hasDb, $adminRepo, $adminLayout) {
    $auth->requireLogin();
    $clubId = isset($_GET['klub']) && $_GET['klub'] !== '' ? (int)$_GET['klub'] : null;
    $q      = trim((string)($_GET['q'] ?? ''));
    return $adminLayout('admin/athletes_list', [
        'title'   => 'Zawodnicy · Panel',
        'rows'    => $hasDb ? $adminRepo->listAthletes($clubId, $q ?: null) : [],
        'clubs'   => $hasDb ? $adminRepo->listClubs() : [],
        'q'       => $q,
        'clubId'  => $clubId,
        'flashes' => Flash::pull(),
    ]);
});

$router->get('/admin/zawodnicy/new', static function () use ($auth, $hasDb, $adminRepo, $adminLayout) {
    $auth->requireLogin();
    return $adminLayout('admin/athlete_form', [
        'title' => 'Nowy zawodnik',
        'athlete' => null,
        'clubs' => $hasDb ? $adminRepo->listClubs() : [],
        'preselect_club' => isset($_GET['klub']) ? (int)$_GET['klub'] : null,
        'flashes' => Flash::pull(),
    ]);
});

$router->get('/admin/zawodnicy/{id}', static function (array $p) use ($auth, $hasDb, $adminRepo, $resultRepo, $edition, $roundRepo, $adminLayout) {
    $auth->requireLogin();
    if (!$hasDb) { http_response_code(404); return $adminLayout('admin/athlete_form', ['title'=>'Brak bazy','athlete'=>null,'clubs'=>[],'flashes'=>Flash::pull()]); }
    $athlete = $adminRepo->findAthlete((int)$p['id']);
    if (!$athlete) { http_response_code(404); return $adminLayout('admin/athlete_form', ['title'=>'Brak zawodnika','athlete'=>null,'clubs'=>[],'flashes'=>Flash::pull()]); }
    return $adminLayout('admin/athlete_form', [
        'title'   => 'Edycja: ' . $athlete['last_name'] . ' ' . $athlete['first_name'],
        'athlete' => $athlete,
        'clubs'   => $adminRepo->listClubs(),
        'preselect_club' => (int)$athlete['club_id'],
        'rounds'  => $roundRepo->forEdition((int)$edition['id']),
        'disciplines' => $resultRepo->disciplines(),
        'scores'  => $adminRepo->athleteScores((int)$athlete['id'], (int)$edition['id']),
        'flashes' => Flash::pull(),
    ]);
});

$router->post('/admin/zawodnicy', static function () use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { Flash::add('err','Brak bazy.'); header('Location: /admin/zawodnicy'); exit; }
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    if ($first === '' || $last === '') {
        Flash::add('err','Imię i nazwisko są wymagane.');
        header('Location: /admin/zawodnicy/' . ($id ?? 'new')); exit;
    }
    $club  = (int)($_POST['club_id'] ?? 0) ?: null;
    $slug  = trim($_POST['slug'] ?? '') ?: Slugger::make($first . '-' . $last . ($club ? '-' . $club : ''));
    $data = [
        'club_id'    => $club,
        'first_name' => $first,
        'last_name'  => $last,
        'birth_year' => (int)($_POST['birth_year'] ?? 0) ?: null,
        'gender'     => in_array($_POST['gender'] ?? '', ['M','K'], true) ? $_POST['gender'] : null,
        'primary_discipline' => in_array($_POST['primary_discipline'] ?? '', ['KPN','PPN','BOTH'], true) ? $_POST['primary_discipline'] : null,
        'slug'       => $slug,
        'license_no' => trim($_POST['license_no'] ?? '') ?: null,
    ];
    try {
        $newId = $adminRepo->saveAthlete($id, $data);
        Flash::add('ok','Zapisano zawodnika.');
        header('Location: /admin/zawodnicy/' . $newId); exit;
    } catch (\Throwable $e) {
        Flash::add('err','Błąd zapisu: '.$e->getMessage());
        header('Location: /admin/zawodnicy' . ($id ? '/' . $id : '/new')); exit;
    }
});

$router->post('/admin/zawodnicy/{id}/delete', static function (array $p) use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if ($hasDb) { $adminRepo->deleteAthlete((int)$p['id']); Flash::add('ok','Zawodnik usunięty.'); }
    header('Location: /admin/zawodnicy'); exit;
});

$router->post('/admin/zawodnicy/{id}/wynik', static function (array $p) use ($auth, $hasDb, $adminRepo, $edition) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { header('Location: /admin/zawodnicy'); exit; }
    $athleteId   = (int)$p['id'];
    $roundId     = (int)($_POST['round_id'] ?? 0);
    $disciplineId= (int)($_POST['discipline_id'] ?? 0);
    $scoreRaw    = str_replace(',', '.', (string)($_POST['score'] ?? ''));
    if (!$roundId || !$disciplineId || !is_numeric($scoreRaw)) {
        Flash::add('err','Niepoprawne dane wyniku.');
        header('Location: /admin/zawodnicy/' . $athleteId); exit;
    }
    $athlete = $adminRepo->findAthlete($athleteId);
    $teamId  = null;
    if ($athlete && $athlete['club_id']) {
        $teams = $adminRepo->teamsForClub((int)$athlete['club_id'], (int)$edition['id']);
        foreach ($teams as $t) { if ((int)$t['discipline_id'] === $disciplineId) { $teamId = (int)$t['id']; break; } }
    }
    $adminRepo->upsertAthleteScore($athleteId, $roundId, $disciplineId, $teamId, (float)$scoreRaw);
    Flash::add('ok','Wynik zapisany.');
    header('Location: /admin/zawodnicy/' . $athleteId); exit;
});

$router->post('/admin/zawodnicy/{id}/wynik/{sid}/delete', static function (array $p) use ($auth, $hasDb, $adminRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if ($hasDb) { $adminRepo->deleteAthleteScore((int)$p['sid']); Flash::add('ok','Wynik usunięty.'); }
    header('Location: /admin/zawodnicy/' . (int)$p['id']); exit;
});

/* ---- CRUD Sponsorzy ---- */
$router->get('/admin/sponsorzy', static function () use ($auth, $hasDb, $mediaRepo, $adminLayout) {
    $auth->requireLogin();
    return $adminLayout('admin/sponsors_list', [
        'title' => 'Sponsorzy i partnerzy · Panel',
        'rows'  => $hasDb ? $mediaRepo->listSponsors() : [],
        'flashes' => Flash::pull(),
    ]);
});

$router->get('/admin/sponsorzy/new', static function () use ($auth, $adminLayout) {
    $auth->requireLogin();
    return $adminLayout('admin/sponsor_form', [
        'title' => 'Nowy partner', 'sponsor' => null, 'flashes' => Flash::pull(),
    ]);
});

$router->get('/admin/sponsorzy/{id}', static function (array $p) use ($auth, $hasDb, $mediaRepo, $adminLayout) {
    $auth->requireLogin();
    $sp = $hasDb ? $mediaRepo->findSponsor((int)$p['id']) : null;
    if (!$sp) { http_response_code(404); return $adminLayout('admin/sponsor_form', ['title'=>'Brak','sponsor'=>null,'flashes'=>Flash::pull()]); }
    return $adminLayout('admin/sponsor_form', [
        'title' => 'Edycja: ' . $sp['name'], 'sponsor' => $sp, 'flashes' => Flash::pull(),
    ]);
});

$router->post('/admin/sponsorzy', static function () use ($auth, $hasDb, $mediaRepo, $edition) {
    $auth->requireLogin(); Csrf::requireValid();
    if (!$hasDb) { Flash::add('err','Brak bazy.'); header('Location: /admin/sponsorzy'); exit; }
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        Flash::add('err','Nazwa jest wymagana.');
        header('Location: /admin/sponsorzy/' . ($id ?? 'new')); exit;
    }
    $allowedCat   = ['patronat_honorowy','sponsor_glowny','sponsor','partner','partner_medialny','partner_techniczny'];
    $allowedTier  = ['zloto','srebro','braz','partner','patronat'];
    $allowedScope = ['liga','spg','final','wszystko'];
    $data = [
        'edition_id' => (int)($_POST['edition_id'] ?? 0) ?: (int)$edition['id'] ?: null,
        'name'       => $name,
        'tier'       => in_array($_POST['tier'] ?? '', $allowedTier, true) ? $_POST['tier'] : 'partner',
        'category'   => in_array($_POST['category'] ?? '', $allowedCat, true) ? $_POST['category'] : 'partner',
        'scope'      => in_array($_POST['scope'] ?? '', $allowedScope, true) ? $_POST['scope'] : 'wszystko',
        'logo'       => trim($_POST['logo'] ?? '') ?: null,
        'url'        => trim($_POST['url']  ?? '') ?: null,
        'instagram_url' => trim($_POST['instagram_url'] ?? '') ?: null,
        'facebook_url'  => trim($_POST['facebook_url']  ?? '') ?: null,
        'description'   => trim($_POST['description'] ?? '') ?: null,
        'sort'       => (int)($_POST['sort'] ?? 100),
        'is_visible' => !empty($_POST['is_visible']) ? 1 : 0,
    ];
    try {
        $newId = $mediaRepo->saveSponsor($id, $data);
        Flash::add('ok','Zapisano partnera #' . $newId . '.');
        header('Location: /admin/sponsorzy/' . $newId); exit;
    } catch (\Throwable $e) {
        Flash::add('err','Błąd: ' . $e->getMessage());
        header('Location: /admin/sponsorzy' . ($id ? '/' . $id : '/new')); exit;
    }
});

$router->post('/admin/sponsorzy/{id}/delete', static function (array $p) use ($auth, $hasDb, $mediaRepo) {
    $auth->requireLogin(); Csrf::requireValid();
    if ($hasDb) { $mediaRepo->deleteSponsor((int)$p['id']); Flash::add('ok','Partner usunięty.'); }
    header('Location: /admin/sponsorzy'); exit;
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

/* SEO */
$router->get('/robots.txt', static function () use ($config) {
    header('Content-Type: text/plain; charset=utf-8');
    return "User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: " . ($config['app']['base_url'] ?? '') . "/sitemap.xml\n";
});

$router->get('/sitemap.xml', static function () use ($config, $hasDb, $contentRepo, $clubRepo, $edition, $editionRepo) {
    header('Content-Type: application/xml; charset=utf-8');
    $base = rtrim((string)($config['app']['base_url'] ?? 'https://example.pl'), '/');
    $now  = date('Y-m-d');
    $urls = ['/', '/wyniki', '/terminarz', '/regulamin', '/final-puchar-gdyni',
             '/spg', '/kluby', '/aktualnosci', '/kontakt', '/archiwum'];
    if ($hasDb) {
        foreach ($contentRepo->latestNews((int)$edition['id'], 100) as $n) {
            $urls[] = '/aktualnosci/' . $n['slug'];
        }
        foreach ($clubRepo->all() as $c) {
            if (!empty($c['slug'])) { $urls[] = '/klub/' . $c['slug']; }
        }
        foreach ($editionRepo->archive() as $ed) {
            $urls[] = '/archiwum/' . (int)$ed['year'];
        }
    }
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach (array_unique($urls) as $u) {
        $xml .= "  <url><loc>" . htmlspecialchars($base . $u, ENT_XML1) . "</loc><lastmod>$now</lastmod></url>\n";
    }
    $xml .= '</urlset>';
    return $xml;
});

$router->get('/og.svg', static function () use ($edition) {
    header('Content-Type: image/svg+xml; charset=utf-8');
    $title = htmlspecialchars((string)($_GET['t'] ?? $edition['title']), ENT_QUOTES);
    $sub   = htmlspecialchars((string)($_GET['s'] ?? 'Liga Młodzieżowa PZSS 2026'), ENT_QUOTES);
    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" width="1200" height="630">
  <defs>
    <linearGradient id="g" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0" stop-color="#0a1426"/>
      <stop offset=".6" stop-color="#14223f"/>
      <stop offset="1" stop-color="#1a2c52"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="630" fill="url(#g)"/>
  <circle cx="980" cy="120" r="180" fill="#b00020" opacity=".25"/>
  <circle cx="200" cy="540" r="220" fill="#d4af37" opacity=".18"/>
  <text x="80" y="220" fill="#d4af37" font-family="Inter, sans-serif" font-size="28" font-weight="700" letter-spacing="3">PZSS · STRZELECTWO MŁODZIEŻOWE</text>
  <text x="80" y="340" fill="#fff"     font-family="Barlow, sans-serif" font-size="96" font-weight="900">$title</text>
  <text x="80" y="420" fill="#cbd5e1"  font-family="Inter, sans-serif" font-size="32" font-weight="500">$sub</text>
  <rect x="80" y="500" width="120" height="6" fill="#b00020"/>
</svg>
SVG;
});

/* JSON API */
$json = function ($data, int $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
};

$router->get('/api/standings.json', static function () use ($json, $edition, $hasDb, $resultRepo) {
    if (!$hasDb) return $json(['error' => 'database unavailable'], 503);
    $out = ['edition' => ['year' => (int)$edition['year'], 'title' => $edition['title']], 'standings' => []];
    foreach ($resultRepo->disciplines() as $d) {
        $out['standings'][$d['code']] = [
            'discipline' => $d['name'],
            'rows'       => $resultRepo->standings((int)$edition['id'], (int)$d['id']),
        ];
    }
    return $json($out);
});

$router->get('/api/rounds-table.json', static function () use ($json, $edition, $hasDb, $resultRepo) {
    if (!$hasDb) return $json(['error' => 'database unavailable'], 503);
    $out = ['edition' => ['year' => (int)$edition['year']], 'disciplines' => []];
    foreach ($resultRepo->disciplines() as $d) {
        $out['disciplines'][$d['code']] = $resultRepo->roundsTable((int)$edition['id'], (int)$d['id']);
    }
    return $json($out);
});

$router->get('/api/clubs.json', static function () use ($json, $edition, $hasDb, $clubRepo) {
    if (!$hasDb) return $json(['error' => 'database unavailable'], 503);
    return $json(['clubs' => $clubRepo->clubsForEdition((int)$edition['id'])]);
});

$router->get('/api/rounds.json', static function () use ($json, $edition, $hasDb, $roundRepo) {
    if (!$hasDb) return $json(['error' => 'database unavailable'], 503);
    return $json(['rounds' => $roundRepo->forEdition((int)$edition['id'])]);
});

echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
