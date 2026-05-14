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
