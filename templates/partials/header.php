<?php use App\Core\View; $e = fn($v) => View::e($v); ?>
<header class="site-header">
    <div class="container header-grid">
        <a href="/" class="brand" aria-label="Strona główna">
            <img src="/assets/img/logo-pzss.svg" alt="PZSS" class="brand-logo">
            <span class="brand-text">
                <span class="brand-line-1">Liga Młodzieżowa</span>
                <span class="brand-line-2">PZSS <?= (int)$edition['year'] ?></span>
            </span>
        </a>
        <button class="nav-toggle" aria-label="Menu" aria-expanded="false" aria-controls="primary-nav">
            <span></span><span></span><span></span>
        </button>
        <nav id="primary-nav" class="primary-nav" aria-label="Menu główne">
            <ul>
                <li><a href="/">Start</a></li>
                <li><a href="/wyniki">Wyniki</a></li>
                <li><a href="/terminarz">Terminarz</a></li>
                <li><a href="/regulamin">Regulamin</a></li>
                <li><a href="/final-puchar-gdyni" class="hl">Finał &amp; Puchar Gdyni</a></li>
                <li><a href="/aktualnosci">Aktualności</a></li>
                <li><a href="/kontakt">Kontakt</a></li>
            </ul>
        </nav>
    </div>
</header>
