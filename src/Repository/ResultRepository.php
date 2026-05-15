<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ResultRepository
{
    public function __construct(private PDO $pdo) {}

    public function disciplines(): array
    {
        return $this->pdo->query('SELECT * FROM disciplines ORDER BY sort ASC')->fetchAll();
    }

    /**
     * Ranking zespołów wg najwyższego wyniku z dotychczasowych rund.
     * @return array<int,array<string,mixed>>
     */
    public function standings(int $editionId, int $disciplineId): array
    {
        $sql = "
            SELECT
                t.id           AS team_id,
                t.display_name AS team,
                c.city         AS city,
                MAX(ts.score)  AS best_score,
                COUNT(ts.id)   AS rounds_played,
                (SELECT r.short_label
                 FROM team_scores ts2
                 JOIN rounds r ON r.id = ts2.round_id
                 WHERE ts2.team_id = t.id
                 ORDER BY ts2.score DESC LIMIT 1) AS best_round
            FROM teams t
            JOIN clubs c ON c.id = t.club_id
            LEFT JOIN team_scores ts ON ts.team_id = t.id
            WHERE t.edition_id = :e AND t.discipline_id = :d
            GROUP BY t.id
            ORDER BY best_score DESC, t.display_name ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':e' => $editionId, ':d' => $disciplineId]);
        $rows = $stmt->fetchAll();

        $place = 0;
        foreach ($rows as &$row) {
            $row['place'] = ++$place;
            $row['qualified'] = $place <= 8;
            $row['best_score'] = $row['best_score'] !== null ? (float)$row['best_score'] : null;
        }
        return $rows;
    }

    /**
     * Widok "tabela rund": dla każdego zespołu — wynik z każdej rundy (kolumny),
     * najlepszy wynik z sezonu i status Q (top-8).
     *
     * @return array{rounds: list<array>, rows: list<array>}
     */
    public function roundsTable(int $editionId, int $disciplineId): array
    {
        $rs = $this->pdo->prepare(
            'SELECT id, code, short_label, sort FROM rounds
             WHERE edition_id = :e AND is_final = 0 ORDER BY sort ASC, number ASC'
        );
        $rs->execute([':e' => $editionId]);
        $rounds = $rs->fetchAll();
        $roundIds = array_column($rounds, 'id');

        $teams = $this->pdo->prepare(
            'SELECT t.id, t.display_name, c.city, c.slug FROM teams t
             JOIN clubs c ON c.id = t.club_id
             WHERE t.edition_id = :e AND t.discipline_id = :d
             ORDER BY t.display_name'
        );
        $teams->execute([':e' => $editionId, ':d' => $disciplineId]);
        $teams = $teams->fetchAll();

        if (!$teams || !$rounds) {
            return ['rounds' => $rounds, 'rows' => []];
        }

        $teamIds = array_column($teams, 'id');
        $in  = implode(',', array_map('intval', $teamIds));
        $rIn = implode(',', array_map('intval', $roundIds));
        $scores = $this->pdo->query(
            "SELECT team_id, round_id, score FROM team_scores
             WHERE team_id IN ($in) AND round_id IN ($rIn)"
        )->fetchAll();

        $byTeam = [];
        foreach ($scores as $s) {
            $byTeam[(int)$s['team_id']][(int)$s['round_id']] = (float)$s['score'];
        }

        $rows = [];
        foreach ($teams as $t) {
            $tid = (int)$t['id'];
            $perRound = $byTeam[$tid] ?? [];
            $best = $perRound ? max($perRound) : null;
            $rows[] = [
                'team_id'       => $tid,
                'team'          => $t['display_name'],
                'city'          => $t['city'],
                'club_slug'     => $t['slug'],
                'per_round'     => $perRound,
                'best_score'    => $best,
                'rounds_played' => count($perRound),
            ];
        }
        usort($rows, function ($a, $b) {
            return ($b['best_score'] ?? -1) <=> ($a['best_score'] ?? -1);
        });
        $place = 0;
        foreach ($rows as &$row) {
            $row['place'] = ++$place;
            $row['qualified'] = $row['best_score'] !== null && $place <= 8;
        }
        return ['rounds' => $rounds, 'rows' => $rows];
    }

    /**
     * Wszystkie wyniki w danej rundzie pogrupowane po dyscyplinie.
     */
    public function roundResults(int $roundId): array
    {
        $sql = "
            SELECT d.id AS discipline_id, d.code AS discipline_code, d.name AS discipline_name,
                   t.display_name AS team, c.city, ts.score
            FROM team_scores ts
            JOIN teams t      ON t.id = ts.team_id
            JOIN clubs c      ON c.id = t.club_id
            JOIN disciplines d ON d.id = t.discipline_id
            WHERE ts.round_id = :r
            ORDER BY d.sort, ts.score DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':r' => $roundId]);
        $grouped = [];
        foreach ($stmt->fetchAll() as $row) {
            $grouped[$row['discipline_code']]['name'] = $row['discipline_name'];
            $grouped[$row['discipline_code']]['rows'][] = $row;
        }
        return $grouped;
    }
}
