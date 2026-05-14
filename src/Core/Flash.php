<?php
declare(strict_types=1);

namespace App\Core;

final class Flash
{
    public static function add(string $type, string $msg): void
    {
        Session::start();
        $f = Session::get('_flash', []);
        $f[] = ['type' => $type, 'msg' => $msg];
        Session::set('_flash', $f);
    }

    public static function pull(): array
    {
        Session::start();
        $f = Session::get('_flash', []);
        Session::forget('_flash');
        return is_array($f) ? $f : [];
    }
}
