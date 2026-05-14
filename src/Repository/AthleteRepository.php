<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class AthleteRepository
{
    public function __construct(private PDO $pdo) {}

    public function forClub(int $clubId): array
    {
        $s = $this->pdo->prepare(
            'SELECT * FROM athletes WHERE club_id = :c ORDER BY last_name, first_name'
        );
        $s->execute([':c' => $clubId]);
        return $s->fetchAll();
    }

    /**
     * Wyniki zawodnika per runda i dyscyplina w danej edycji.
     * Zwraca tabelę: [discipline_code => [round_id => score]], plus suma per runda zespołu.
     */
    public function scoresForClubInEdition(int $clubId, int $editionId): array
    {
        $sql = "
            SELECT a.id AS athlete_id, a.first_name, a.last_name, a.birth_year,
                   d.code AS discipline, d.name AS discipline_name,
                   r.code AS round_code, r.short_label AS round_label,
                   r.number AS round_num, ascore.score
            FROM athletes a
            JOIN athlete_scores ascore ON ascore.athlete_id = a.id
            JOIN rounds r       ON r.id = ascore.round_id AND r.edition_id = :e
            JOIN disciplines d  ON d.id = ascore.discipline_id
            WHERE a.club_id = :c
            ORDER BY d.sort, r.sort, a.last_name, a.first_name
        ";
        $s = $this->pdo->prepare($sql);
        $s->execute([':c' => $clubId, ':e' => $editionId]);
        return $s->fetchAll();
    }
}
