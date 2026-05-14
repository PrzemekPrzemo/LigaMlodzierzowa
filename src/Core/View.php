<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $template, array $data = [], string $layout = 'layout'): string
    {
        $data = array_merge(self::$shared, $data);
        $content = self::renderRaw($template, $data);
        if ($layout === '') {
            return $content;
        }
        $data['content'] = $content;
        return self::renderRaw($layout, $data);
    }

    public static function renderRaw(string $template, array $data = []): string
    {
        $file = dirname(__DIR__, 2) . '/templates/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Brak szablonu: $template");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string)ob_get_clean();
    }

    public static function e(?string $s): string
    {
        return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
