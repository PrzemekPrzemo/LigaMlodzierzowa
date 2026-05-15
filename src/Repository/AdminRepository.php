<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class AdminRepository
{
    public function __construct(private PDO $pdo) {}

    /* ---------- News ---------- */
    public function listNews(int $limit = 100): array
    {
        return $this->pdo->query(
            'SELECT id, title, slug, lead, is_pinned, published_at, edition_id
             FROM news ORDER BY is_pinned DESC, published_at DESC LIMIT ' . (int)$limit
        )->fetchAll();
    }

    public function findNews(int $id): ?array
    {
        $s = $this->pdo->prepare('SELECT * FROM news WHERE id = :i');
        $s->execute([':i' => $id]);
        return $s->fetch() ?: null;
    }

    public function saveNews(?int $id, array $data): int
    {
        if ($id) {
            $s = $this->pdo->prepare(
                'UPDATE news SET edition_id=:e, title=:t, slug=:s, lead=:l, body=:b, is_pinned=:p, published_at=:d WHERE id=:i'
            );
            $s->execute([
                ':e' => $data['edition_id'] ?: null,
                ':t' => $data['title'],
                ':s' => $data['slug'],
                ':l' => $data['lead'],
                ':b' => $data['body'],
                ':p' => $data['is_pinned'] ? 1 : 0,
                ':d' => $data['published_at'],
                ':i' => $id,
            ]);
            return $id;
        }
        $s = $this->pdo->prepare(
            'INSERT INTO news (edition_id, title, slug, lead, body, is_pinned, published_at)
             VALUES (:e,:t,:s,:l,:b,:p,:d)'
        );
        $s->execute([
            ':e' => $data['edition_id'] ?: null,
            ':t' => $data['title'],
            ':s' => $data['slug'],
            ':l' => $data['lead'],
            ':b' => $data['body'],
            ':p' => $data['is_pinned'] ? 1 : 0,
            ':d' => $data['published_at'],
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function deleteNews(int $id): void
    {
        $this->pdo->prepare('DELETE FROM news WHERE id = :i')->execute([':i' => $id]);
    }

    /* ---------- Rounds ---------- */
    public function findRound(int $id): ?array
    {
        $s = $this->pdo->prepare('SELECT * FROM rounds WHERE id = :i');
        $s->execute([':i' => $id]);
        return $s->fetch() ?: null;
    }

    public function setRoundStatus(int $id, string $status): void
    {
        $s = $this->pdo->prepare('UPDATE rounds SET status = :s WHERE id = :i');
        $s->execute([':s' => $status, ':i' => $id]);
    }

    /* ---------- Team scores ---------- */
    public function scoresForRound(int $roundId): array
    {
        $sql = "SELECT ts.id, ts.score, ts.team_id, ts.round_id,
                       t.display_name, t.discipline_id, d.code AS discipline_code, d.short AS discipline
                FROM team_scores ts
                JOIN teams t      ON t.id = ts.team_id
                JOIN disciplines d ON d.id = t.discipline_id
                WHERE ts.round_id = :r
                ORDER BY d.sort, ts.score DESC";
        $s = $this->pdo->prepare($sql);
        $s->execute([':r' => $roundId]);
        return $s->fetchAll();
    }

    public function teamsForDiscipline(int $editionId, int $disciplineId): array
    {
        $s = $this->pdo->prepare(
            'SELECT t.id, t.display_name FROM teams t
             WHERE t.edition_id = :e AND t.discipline_id = :d
             ORDER BY t.display_name'
        );
        $s->execute([':e' => $editionId, ':d' => $disciplineId]);
        return $s->fetchAll();
    }

    public function upsertScore(int $teamId, int $roundId, float $score): void
    {
        $s = $this->pdo->prepare(
            'INSERT INTO team_scores (team_id, round_id, score) VALUES (:t, :r, :s)
             ON DUPLICATE KEY UPDATE score = VALUES(score)'
        );
        $s->execute([':t' => $teamId, ':r' => $roundId, ':s' => $score]);
    }

    public function deleteScore(int $id): void
    {
        $this->pdo->prepare('DELETE FROM team_scores WHERE id = :i')->execute([':i' => $id]);
    }

    /* ---------- Clubs ---------- */
    public function listClubs(): array
    {
        return $this->pdo->query(
            'SELECT c.*, (SELECT COUNT(*) FROM teams t WHERE t.club_id = c.id) AS teams_count
             FROM clubs c ORDER BY c.name'
        )->fetchAll();
    }

    public function findClub(int $id): ?array
    {
        $s = $this->pdo->prepare('SELECT * FROM clubs WHERE id = :i');
        $s->execute([':i' => $id]);
        return $s->fetch() ?: null;
    }

    public function findClubBySlug(string $slug): ?array
    {
        $s = $this->pdo->prepare('SELECT * FROM clubs WHERE slug = :s');
        $s->execute([':s' => $slug]);
        return $s->fetch() ?: null;
    }

    public function saveClub(?int $id, array $data): int
    {
        if ($id) {
            $s = $this->pdo->prepare(
                'UPDATE clubs SET name=:n, short=:sh, slug=:sl, city=:c, region=:r, logo=:lo, website=:w WHERE id=:i'
            );
            $s->execute([
                ':n'=>$data['name'], ':sh'=>$data['short'] ?: null, ':sl'=>$data['slug'],
                ':c'=>$data['city'] ?: null, ':r'=>$data['region'] ?: null,
                ':lo'=>$data['logo'] ?: null, ':w'=>$data['website'] ?: null, ':i'=>$id,
            ]);
            return $id;
        }
        $s = $this->pdo->prepare(
            'INSERT INTO clubs (name, short, slug, city, region, logo, website)
             VALUES (:n,:sh,:sl,:c,:r,:lo,:w)'
        );
        $s->execute([
            ':n'=>$data['name'], ':sh'=>$data['short'] ?: null, ':sl'=>$data['slug'],
            ':c'=>$data['city'] ?: null, ':r'=>$data['region'] ?: null,
            ':lo'=>$data['logo'] ?: null, ':w'=>$data['website'] ?: null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function deleteClub(int $id): void
    {
        $this->pdo->prepare('DELETE FROM clubs WHERE id = :i')->execute([':i' => $id]);
    }

    public function teamsForClub(int $clubId, int $editionId): array
    {
        $s = $this->pdo->prepare(
            'SELECT t.id, t.discipline_id, d.code, d.name AS discipline_name
             FROM teams t JOIN disciplines d ON d.id = t.discipline_id
             WHERE t.club_id = :c AND t.edition_id = :e
             ORDER BY d.sort'
        );
        $s->execute([':c' => $clubId, ':e' => $editionId]);
        return $s->fetchAll();
    }

    public function createTeam(int $editionId, int $clubId, int $disciplineId, string $displayName): int
    {
        $s = $this->pdo->prepare(
            'INSERT IGNORE INTO teams (edition_id, club_id, discipline_id, display_name)
             VALUES (:e, :c, :d, :n)'
        );
        $s->execute([':e'=>$editionId, ':c'=>$clubId, ':d'=>$disciplineId, ':n'=>$displayName]);
        $id = (int)$this->pdo->lastInsertId();
        if ($id === 0) {
            $find = $this->pdo->prepare('SELECT id FROM teams WHERE edition_id=:e AND club_id=:c AND discipline_id=:d');
            $find->execute([':e'=>$editionId, ':c'=>$clubId, ':d'=>$disciplineId]);
            $id = (int)($find->fetchColumn() ?: 0);
        }
        return $id;
    }

    public function deleteTeam(int $teamId): void
    {
        $this->pdo->prepare('DELETE FROM teams WHERE id = :i')->execute([':i' => $teamId]);
    }

    /* ---------- Athletes ---------- */
    public function listAthletes(?int $clubId = null, ?string $q = null, int $limit = 500): array
    {
        $sql = "SELECT a.*, c.name AS club_name, c.slug AS club_slug
                FROM athletes a LEFT JOIN clubs c ON c.id = a.club_id WHERE 1=1";
        $args = [];
        if ($clubId) { $sql .= ' AND a.club_id = :c'; $args[':c'] = $clubId; }
        if ($q !== null && $q !== '') {
            $sql .= ' AND (a.last_name LIKE :q OR a.first_name LIKE :q)';
            $args[':q'] = '%' . $q . '%';
        }
        $sql .= ' ORDER BY a.last_name, a.first_name LIMIT ' . (int)$limit;
        $s = $this->pdo->prepare($sql);
        $s->execute($args);
        return $s->fetchAll();
    }

    public function findAthlete(int $id): ?array
    {
        $s = $this->pdo->prepare('SELECT * FROM athletes WHERE id = :i');
        $s->execute([':i' => $id]);
        return $s->fetch() ?: null;
    }

    public function saveAthlete(?int $id, array $data): int
    {
        if ($id) {
            $s = $this->pdo->prepare(
                'UPDATE athletes SET club_id=:c, first_name=:fn, last_name=:ln, birth_year=:by, gender=:g, slug=:sl, license_no=:l WHERE id=:i'
            );
            $s->execute([
                ':c'=>$data['club_id'] ?: null, ':fn'=>$data['first_name'], ':ln'=>$data['last_name'],
                ':by'=>$data['birth_year'] ?: null, ':g'=>$data['gender'] ?: null,
                ':sl'=>$data['slug'] ?: null, ':l'=>$data['license_no'] ?: null, ':i'=>$id,
            ]);
            return $id;
        }
        $s = $this->pdo->prepare(
            'INSERT INTO athletes (club_id, first_name, last_name, birth_year, gender, slug, license_no)
             VALUES (:c,:fn,:ln,:by,:g,:sl,:l)'
        );
        $s->execute([
            ':c'=>$data['club_id'] ?: null, ':fn'=>$data['first_name'], ':ln'=>$data['last_name'],
            ':by'=>$data['birth_year'] ?: null, ':g'=>$data['gender'] ?: null,
            ':sl'=>$data['slug'] ?: null, ':l'=>$data['license_no'] ?: null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function deleteAthlete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM athletes WHERE id = :i')->execute([':i' => $id]);
    }

    public function athleteScores(int $athleteId, int $editionId): array
    {
        $s = $this->pdo->prepare(
            "SELECT ascore.id, ascore.round_id, ascore.discipline_id, ascore.score,
                    r.code AS round_code, r.short_label, r.sort AS round_sort,
                    d.code AS discipline_code, d.name AS discipline_name, d.sort AS d_sort
             FROM athlete_scores ascore
             JOIN rounds r ON r.id = ascore.round_id AND r.edition_id = :e
             JOIN disciplines d ON d.id = ascore.discipline_id
             WHERE ascore.athlete_id = :a
             ORDER BY d.sort, r.sort"
        );
        $s->execute([':a' => $athleteId, ':e' => $editionId]);
        return $s->fetchAll();
    }

    public function upsertAthleteScore(int $athleteId, int $roundId, int $disciplineId, ?int $teamId, float $score): void
    {
        $s = $this->pdo->prepare(
            'INSERT INTO athlete_scores (athlete_id, round_id, discipline_id, team_id, score)
             VALUES (:a, :r, :d, :t, :s)
             ON DUPLICATE KEY UPDATE score = VALUES(score), team_id = VALUES(team_id)'
        );
        $s->execute([':a'=>$athleteId, ':r'=>$roundId, ':d'=>$disciplineId, ':t'=>$teamId, ':s'=>$score]);
    }

    public function deleteAthleteScore(int $id): void
    {
        $this->pdo->prepare('DELETE FROM athlete_scores WHERE id = :i')->execute([':i' => $id]);
    }

    /* ---------- Stats ---------- */
    public function stats(int $editionId): array
    {
        $row = function (string $sql, array $args = []) {
            $s = $this->pdo->prepare($sql);
            $s->execute($args);
            return $s->fetch();
        };
        return [
            'teams_total'   => (int)($row('SELECT COUNT(*) AS c FROM teams WHERE edition_id = :e', [':e' => $editionId])['c'] ?? 0),
            'scores_total'  => (int)($row('SELECT COUNT(*) AS c FROM team_scores ts JOIN teams t ON t.id = ts.team_id WHERE t.edition_id = :e', [':e' => $editionId])['c'] ?? 0),
            'rounds_done'   => (int)($row("SELECT COUNT(*) AS c FROM rounds WHERE edition_id = :e AND status='finished'", [':e' => $editionId])['c'] ?? 0),
            'news_total'    => (int)($row('SELECT COUNT(*) AS c FROM news WHERE edition_id = :e OR edition_id IS NULL', [':e' => $editionId])['c'] ?? 0),
        ];
    }
}
