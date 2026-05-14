<?php use App\Core\View; use App\Core\Csrf; $e = fn($v) => View::e($v); $p = $post; ?>
<header class="admin-head">
    <h1><?= $p ? 'Edycja wpisu' : 'Nowy wpis' ?></h1>
    <a class="btn btn-ghost-d" href="/admin/news">← Lista</a>
</header>

<form method="post" action="/admin/news" class="form form-grid">
    <?= Csrf::field() ?>
    <?php if ($p): ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><?php endif; ?>
    <label class="col-12">
        <span>Tytuł</span>
        <input name="title" required value="<?= $e($p['title'] ?? '') ?>">
    </label>
    <label class="col-8">
        <span>Slug (URL)</span>
        <input name="slug" required value="<?= $e($p['slug'] ?? '') ?>">
    </label>
    <label class="col-4">
        <span>Data publikacji</span>
        <input name="published_at" type="datetime-local" value="<?= $e($p ? date('Y-m-d\TH:i', strtotime($p['published_at'])) : date('Y-m-d\TH:i')) ?>">
    </label>
    <label class="col-12">
        <span>Lead (krótki opis)</span>
        <textarea name="lead" rows="2"><?= $e($p['lead'] ?? '') ?></textarea>
    </label>
    <label class="col-12">
        <span>Treść (HTML)</span>
        <textarea name="body" rows="10"><?= $e($p['body'] ?? '') ?></textarea>
    </label>
    <label class="col-12 checkbox">
        <input type="checkbox" name="is_pinned" value="1" <?= !empty($p['is_pinned']) ? 'checked' : '' ?>>
        <span>Przypięty (na górze)</span>
    </label>
    <div class="col-12 actions-bar">
        <button class="btn btn-primary" type="submit"><?= $p ? 'Zapisz zmiany' : 'Dodaj wpis' ?></button>
        <a class="btn btn-ghost-d" href="/admin/news">Anuluj</a>
    </div>
</form>
