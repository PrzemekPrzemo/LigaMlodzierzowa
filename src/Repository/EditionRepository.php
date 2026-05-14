<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class EditionRepository
{
    public function __construct(private PDO $pdo) {}

    public function active(): ?array
    {
        $stmt = $this->pdo->query('SELECT * FROM editions WHERE is_active = 1 ORDER BY year DESC LIMIT 1');
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function byYear(int $year): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM editions WHERE year = :y');
        $stmt->execute([':y' => $year]);
        return $stmt->fetch() ?: null;
    }
}
