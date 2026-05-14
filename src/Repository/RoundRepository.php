<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class RoundRepository
{
    public function __construct(private PDO $pdo) {}

    public function forEdition(int $editionId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM rounds WHERE edition_id = :e ORDER BY number ASC'
        );
        $stmt->execute([':e' => $editionId]);
        return $stmt->fetchAll();
    }

    public function nextUpcoming(int $editionId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM rounds WHERE edition_id = :e AND status <> 'finished'
             ORDER BY COALESCE(starts_on, '9999-12-31') ASC LIMIT 1"
        );
        $stmt->execute([':e' => $editionId]);
        return $stmt->fetch() ?: null;
    }
}
