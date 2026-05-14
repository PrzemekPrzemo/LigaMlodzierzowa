<?php
use App\Core\View;
$e = fn($v) => View::e($v);
?>
<section class="page-head">
    <div class="container">
        <p class="muted"><a href="/aktualnosci">← Wróć do aktualności</a></p>
        <h1><?= $e($post['title']) ?></h1>
        <p class="muted"><?= $e(date('d.m.Y', strtotime($post['published_at']))) ?></p>
    </div>
</section>
<article class="container section prose">
    <p class="lead"><?= $e($post['lead']) ?></p>
    <?= $post['body'] /* HTML z bazy — kontrolowany przez admina */ ?>
</article>
