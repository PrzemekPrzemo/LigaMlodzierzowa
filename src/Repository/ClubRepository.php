<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ClubRepository
{
    public function __construct(private PDO $pdo) {}

    /** Lista klubów aktywnych w danej edycji + KPI. */
    public function clubsForEdition(int $editionId): array
    {
        $sql = "
            SELECT c.id, c.name, c.short, c.city, c.slug, c.logo,
                   COUNT(DISTINCT t.id) AS teams_count,
                   MAX(ts.score)        AS best_score,
                   (SELECT d.short FROM team_scores ts2
                    JOIN teams t2 ON t2.id = ts2.team_id
                    JOIN disciplines d ON d.id = t2.discipline_id
                    WHERE t2.club_id = c.id AND t2.edition_id = :e
                    ORDER BY ts2.score DESC LIMIT 1) AS best_discipline
            FROM clubs c
            JOIN teams t       ON t.club_id = c.id AND t.edition_id = :e
            LEFT JOIN team_scores ts ON ts.team_id = t.id
            GROUP BY c.id
            ORDER BY best_score DESC, c.name ASC
        ";
        $s = $this->pdo->prepare($sql);
        $s->execute([':e' => $editionId]);
        return $s->fetchAll();
    }

    public function bySlug(string $slug): ?array
    {
        $s = $this->pdo->prepare('SELECT * FROM clubs WHERE slug = :s LIMIT 1');
        $s->execute([':s' => $slug]);
        return $s->fetch() ?: null;
    }

    /** Wszystkie kluby (do admina) */
    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM clubs ORDER BY name')->fetchAll();
    }
}
