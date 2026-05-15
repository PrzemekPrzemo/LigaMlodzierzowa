<?php
declare(strict_types=1);

namespace App\Core;

final class Bootstrap
{
    public static function init(): array
    {
        $configPath = dirname(__DIR__, 2) . '/config/config.php';
        if (!is_file($configPath)) {
            $configPath = dirname(__DIR__, 2) . '/config/config.example.php';
        }
        $config = require $configPath;

        date_default_timezone_set($config['app']['timezone'] ?? 'Europe/Warsaw');
        @setlocale(LC_ALL, $config['app']['locale'] ?? 'pl_PL.UTF-8');
        mb_internal_encoding('UTF-8');

        if ($config['app']['debug'] ?? false) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            ini_set('display_errors', '0');
        }
        // Loguj zawsze do PHP error_log — produkcyjnie bardzo pomocne przy 500
        ini_set('log_errors', '1');
        set_exception_handler(static function (\Throwable $e): void {
            error_log('[Liga] Uncaught: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo '<!doctype html><meta charset="utf-8"><title>500</title>'
               . '<div style="font-family:system-ui;padding:3rem;max-width:640px;margin:auto">'
               . '<h1 style="color:#b00020">Błąd serwera</h1>'
               . '<p>Coś poszło nie tak. Skontaktuj się z administratorem.</p>'
               . '<p><a href="/">← Strona główna</a></p></div>';
        });

        spl_autoload_register(static function (string $class): void {
            if (!str_starts_with($class, 'App\\')) {
                return;
            }
            $rel = str_replace('\\', '/', substr($class, 4)) . '.php';
            $file = dirname(__DIR__) . '/' . $rel;
            if (is_file($file)) {
                require $file;
            }
        });

        return $config;
    }
}
