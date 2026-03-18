<?php
namespace App\Framework;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo) return self::$pdo;

        $file = __DIR__ . '/../../Config/database.php';
        if (!file_exists($file)) {
            throw new \Exception("Missing config: app/Config/database.php (expected at $file)");
        }

        $c = require $file;

        $opts = $c['opts'] ?? [];
        $opts = $opts + [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        self::$pdo = new PDO($c['dsn'], $c['user'], $c['pass'], $opts);
        return self::$pdo;
    }
}