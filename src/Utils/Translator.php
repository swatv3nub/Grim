<?php
namespace Grim\Utils;

class Translator
{
    private static $lang = 'en';
    private static $translations = [];

    public static function setLang(string $lang): void
    {
        self::$lang = $lang;
        $file = __DIR__ . '/../../lang/' . $lang . '.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        } else {
            self::$translations = require __DIR__ . '/../../lang/en.php';
        }
    }

    public static function t(string $key): string
    {
        return self::$translations[$key] ?? $key;
    }
}
