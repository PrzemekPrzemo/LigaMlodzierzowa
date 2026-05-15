<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class MediaRepository
{
    public function __construct(private PDO $pdo) {}

    public function sponsors(int $editionId, ?string $scope = null): array
    {
        $sql = 'SELECT * FROM sponsors WHERE is_visible = 1 AND (edition_id IS NULL OR edition_id = :e)';
        $args = [':e' => $editionId];
        if ($scope) { $sql .= " AND (scope = :s OR scope = 'wszystko')"; $args[':s'] = $scope; }
        $sql .= ' ORDER BY FIELD(tier, "patronat","zloto","srebro","braz","partner"), sort, name';
        $s = $this->pdo->prepare($sql);
        $s->execute($args);
        return $s->fetchAll();
    }

    public function gallery(int $editionId, int $limit = 30): array
    {
        $s = $this->pdo->prepare(
            'SELECT * FROM gallery_items WHERE edition_id = :e ORDER BY published_at DESC LIMIT ' . (int)$limit
        );
        $s->execute([':e' => $editionId]);
        return $s->fetchAll();
    }

    public function liveStreams(int $editionId): array
    {
        $s = $this->pdo->prepare(
            'SELECT * FROM live_streams WHERE edition_id = :e
             ORDER BY FIELD(status,"live","upcoming","ended"), sort, starts_at'
        );
        $s->execute([':e' => $editionId]);
        return $s->fetchAll();
    }
}
