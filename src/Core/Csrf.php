<?php
declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public const FIELD = '_csrf';

    public static function token(): string
    {
        Session::start();
        $t = Session::get('_csrf');
        if (!is_string($t) || $t === '') {
            $t = bin2hex(random_bytes(32));
            Session::set('_csrf', $t);
        }
        return $t;
    }

    public static function field(): string
    {
        return '<input type="hidden" name="' . self::FIELD . '" value="' . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    public static function check(?string $given): bool
    {
        $t = Session::get('_csrf');
        return is_string($t) && is_string($given) && hash_equals($t, $given);
    }

    public static function requireValid(): void
    {
        $given = $_POST[self::FIELD] ?? null;
        if (!self::check(is_string($given) ? $given : null)) {
            http_response_code(419);
            exit('Invalid CSRF token.');
        }
    }
}
