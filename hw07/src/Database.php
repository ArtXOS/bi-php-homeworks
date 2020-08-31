<?php

namespace Books;
use PDO;

class Database
{
    private static $pdo;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = new PDO('sqlite:database.sqlite');
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }

}