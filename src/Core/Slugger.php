<?php
declare(strict_types=1);

namespace App\Core;

final class Slugger
{
    private const MAP = [
        'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
        'Ą'=>'a','Ć'=>'c','Ę'=>'e','Ł'=>'l','Ń'=>'n','Ó'=>'o','Ś'=>'s','Ź'=>'z','Ż'=>'z',
    ];

    public static function make(string $text, int $max = 80): string
    {
        $text = strtr($text, self::MAP);
        $text = mb_strtolower($text, 'UTF-8');
        $text = (string)preg_replace('/[^a-z0-9]+/u', '-', $text);
        $text = trim($text, '-');
        if ($text === '') {
            $text = 'item-' . substr(md5((string)microtime(true)), 0, 6);
        }
        if (mb_strlen($text) > $max) {
            $text = mb_substr($text, 0, $max);
            $text = trim($text, '-');
        }
        return $text;
    }
}
