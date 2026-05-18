<?php
use App\Core\View;
use App\Core\Csrf;
use App\Repository\MediaRepository;
$e = fn($v) => View::e($v);
$s = $sponsor;
?>
<header class="admin-head">
    <h1><?= $s ? 'Edycja partnera' : 'Nowy partner' ?></h1>
    <a class="btn btn-ghost-d" href="/admin/sponsorzy">← Lista</a>
</header>

<form method="post" action="/admin/sponsorzy" class="form form-grid">
    <?= Csrf::field() ?>
    <?php if ($s): ?><input type="hidden" name="id" value="<?= (int)$s['id'] ?>"><?php endif; ?>

    <label class="col-8">
        <span>Nazwa</span>
        <input name="name" required value="<?= $e($s['name'] ?? '') ?>" placeholder="np. Polski Związek Strzelectwa Sportowego">
    </label>
    <label class="col-4">
        <span>Sort (mniejsza = wyżej)</span>
        <input name="sort" type="number" value="<?= $e($s['sort'] ?? 100) ?>">
    </label>

    <label class="col-4">
        <span>Kategoria</span>
        <select name="category" required>
            <?php foreach (MediaRepository::CATEGORIES as $k => [$lbl,$cls]): ?>
                <option value="<?= $e($k) ?>" <?= ($s['category'] ?? 'partner') === $k ? 'selected' : '' ?>><?= $e($lbl) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="col-4">
        <span>Tier (ranga)</span>
        <select name="tier">
            <?php foreach (['partner'=>'Partner','zloto'=>'Złoty','srebro'=>'Srebrny','braz'=>'Brązowy','patronat'=>'Patronat'] as $k=>$v): ?>
                <option value="<?= $e($k) ?>" <?= ($s['tier'] ?? 'partner') === $k ? 'selected' : '' ?>><?= $e($v) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="col-4">
        <span>Zakres</span>
        <select name="scope">
            <?php foreach (['wszystko'=>'Wszystko (Liga + SPG + Finał)','liga'=>'Liga Młodzieżowa','spg'=>'Strzelecki Puchar Gdyni','final'=>'Finał'] as $k=>$v): ?>
                <option value="<?= $e($k) ?>" <?= ($s['scope'] ?? 'wszystko') === $k ? 'selected' : '' ?>><?= $e($v) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="col-12">
        <span>Logo (URL — wgraj plik do <code>/assets/img/sponsors/</code> i podaj ścieżkę, np. <code>/assets/img/sponsors/firma.svg</code>)</span>
        <input name="logo" value="<?= $e($s['logo'] ?? '') ?>" placeholder="/assets/img/sponsors/...">
    </label>

    <label class="col-4">
        <span>Strona WWW</span>
        <input name="url" value="<?= $e($s['url'] ?? '') ?>" placeholder="https://...">
    </label>
    <label class="col-4">
        <span>Instagram</span>
        <input name="instagram_url" value="<?= $e($s['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/...">
    </label>
    <label class="col-4">
        <span>Facebook</span>
        <input name="facebook_url" value="<?= $e($s['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/...">
    </label>

    <label class="col-12">
        <span>Opis (krótki)</span>
        <textarea name="description" rows="2"><?= $e($s['description'] ?? '') ?></textarea>
    </label>

    <label class="col-12 checkbox">
        <input type="checkbox" name="is_visible" value="1" <?= !isset($s) || !empty($s['is_visible']) ? 'checked' : '' ?>>
        <span>Widoczny na stronie (uwzględnij w pasku i na <code>/partnerzy</code>)</span>
    </label>

    <div class="col-12 actions-bar">
        <button class="btn btn-primary" type="submit"><?= $s ? 'Zapisz zmiany' : 'Dodaj partnera' ?></button>
        <a class="btn btn-ghost-d" href="/admin/sponsorzy">Anuluj</a>
    </div>
</form>
