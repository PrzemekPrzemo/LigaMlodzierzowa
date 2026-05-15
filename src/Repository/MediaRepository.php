<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class MediaRepository
{
    public const CATEGORIES = [
        'patronat_honorowy'   => ['Patronat honorowy',   'cat-patronat'],
        'sponsor_glowny'      => ['Sponsor główny',      'cat-sponsor-main'],
        'sponsor'             => ['Sponsor',             'cat-sponsor'],
        'partner'             => ['Partner',             'cat-partner'],
        'partner_medialny'    => ['Partner medialny',    'cat-media'],
        'partner_techniczny'  => ['Partner techniczny',  'cat-tech'],
    ];

    public function __construct(private PDO $pdo) {}

    public function sponsors(int $editionId, ?string $scope = null): array
    {
        $sql = 'SELECT * FROM sponsors WHERE is_visible = 1 AND (edition_id IS NULL OR edition_id = :e)';
        $args = [':e' => $editionId];
        if ($scope) { $sql .= " AND (scope = :s OR scope = 'wszystko')"; $args[':s'] = $scope; }
        $sql .= " ORDER BY FIELD(category, 'patronat_honorowy','sponsor_glowny','sponsor','partner','partner_medialny','partner_techniczny'),
                            FIELD(tier, 'patronat','zloto','srebro','braz','partner'), sort, name";
        $s = $this->pdo->prepare($sql);
        $s->execute($args);
        return $s->fetchAll();
    }

    /** Wszyscy widoczni sponsorzy pogrupowani po kategorii (do strony /partnerzy). */
    public function allGroupedByCategory(int $editionId): array
    {
        $rows = $this->sponsors($editionId);
        $grouped = [];
        foreach (self::CATEGORIES as $cat => [$label, $cls]) {
            $grouped[$cat] = ['label' => $label, 'cls' => $cls, 'items' => []];
        }
        foreach ($rows as $r) {
            $cat = $r['category'] ?? 'partner';
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = ['label' => $cat, 'cls' => '', 'items' => []];
            }
            $grouped[$cat]['items'][] = $r;
        }
        // wywal puste
        return array_filter($grouped, static fn($g) => !empty($g['items']));
    }

    /** Lista do paska marquee — tylko widoczni z logotypem. */
    public function forMarquee(int $editionId): array
    {
        $sql = "SELECT * FROM sponsors
                WHERE is_visible = 1
                  AND (edition_id IS NULL OR edition_id = :e)
                ORDER BY FIELD(category, 'patronat_honorowy','sponsor_glowny','sponsor','partner','partner_medialny','partner_techniczny'),
                         sort, name";
        $s = $this->pdo->prepare($sql);
        $s->execute([':e' => $editionId]);
        return $s->fetchAll();
    }

    public function findSponsor(int $id): ?array
    {
        $s = $this->pdo->prepare('SELECT * FROM sponsors WHERE id = :i');
        $s->execute([':i' => $id]);
        return $s->fetch() ?: null;
    }

    public function listSponsors(): array
    {
        return $this->pdo->query(
            "SELECT s.*, e.year FROM sponsors s LEFT JOIN editions e ON e.id = s.edition_id
             ORDER BY FIELD(category, 'patronat_honorowy','sponsor_glowny','sponsor','partner','partner_medialny','partner_techniczny'),
                      sort, name"
        )->fetchAll();
    }

    public function saveSponsor(?int $id, array $data): int
    {
        $cols = ['edition_id','name','tier','category','scope','logo','url','instagram_url','facebook_url','description','sort','is_visible'];
        $params = [];
        foreach ($cols as $c) { $params[":$c"] = $data[$c] ?? null; }
        if ($id) {
            $set = implode(', ', array_map(static fn($c) => "$c=:$c", $cols));
            $params[':id'] = $id;
            $this->pdo->prepare("UPDATE sponsors SET $set WHERE id = :id")->execute($params);
            return $id;
        }
        $colList  = implode(', ', $cols);
        $placeholders = ':' . implode(', :', $cols);
        $this->pdo->prepare("INSERT INTO sponsors ($colList) VALUES ($placeholders)")->execute($params);
        return (int)$this->pdo->lastInsertId();
    }

    public function deleteSponsor(int $id): void
    {
        $this->pdo->prepare('DELETE FROM sponsors WHERE id = :i')->execute([':i' => $id]);
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
