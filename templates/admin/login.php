<?php use App\Core\Csrf; use App\Core\View; $e = fn($v) => View::e($v); ?>
<section class="admin-login">
    <div class="admin-card">
        <img src="/assets/img/logo-pzss.svg" alt="" class="admin-login-logo">
        <h1>Panel administratora</h1>
        <p class="muted">Liga Młodzieżowa PZSS 2026</p>
        <?php foreach (($flashes ?? []) as $f): ?>
            <div class="flash flash-<?= $e($f['type']) ?>"><?= $e($f['msg']) ?></div>
        <?php endforeach; ?>
        <form method="post" action="/admin/login" class="form">
            <?= Csrf::field() ?>
            <label>
                <span>Hasło</span>
                <input type="password" name="password" required autofocus autocomplete="current-password">
            </label>
            <button class="btn btn-primary" type="submit">Zaloguj</button>
        </form>
        <p class="muted small mt-1">Hash hasła ustaw w <code>config/config.php</code> (klucz <code>admin.password_hash</code>).</p>
    </div>
</section>
