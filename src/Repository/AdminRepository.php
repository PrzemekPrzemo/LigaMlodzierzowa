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
