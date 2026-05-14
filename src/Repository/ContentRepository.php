<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ContentRepository
{
    public function __construct(private PDO $pdo) {}

    public function latestNews(int $editionId, int $limit = 6): array
    {
        $sql = 'SELECT * FROM news WHERE edition_id = :e OR edition_id IS NULL
                ORDER BY is_pinned DESC, published_at DESC LIMIT ' . (int)$limit;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':e' => $editionId]);
        return $stmt->fetchAll();
    }

    public function newsBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM news WHERE slug = :s LIMIT 1');
        $stmt->execute([':s' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function documents(int $editionId, ?string $kind = null): array
    {
        $sql = 'SELECT * FROM documents WHERE edition_id = :e';
        $args = [':e' => $editionId];
        if ($kind !== null) {
            $sql .= ' AND kind = :k';
            $args[':k'] = $kind;
        }
        $sql .= ' ORDER BY sort ASC, published_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetchAll();
    }

    public function partners(): array
    {
        return $this->pdo->query('SELECT * FROM partners ORDER BY sort ASC')->fetchAll();
    }
}
