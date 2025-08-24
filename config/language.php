<?php

// crear clase language
namespace App\Config;
class Language
{
    public static function getLanguage(): string
    {
        return $_SESSION['lang'] ?? 'en';
    }

    public static function loadLanguage(string $idioma): array
    {
        $archivo = APP_ROOT . 'lang/' . strtoupper($idioma) . '.php';
        if (file_exists($archivo)) {
            return require $archivo;
        }
        return [];
    }
}

