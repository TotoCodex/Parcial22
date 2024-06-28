<?php
class DB {
    private static $host = "localhost";
    private static $user = "root";
    private static $pwd = "";
    private static $dbName = "parcial2";

    public static function Connect() {
        $dbConnection = 'mysql:host=' . self::$host . ';dbname=' . self::$dbName;
        $pdo = new PDO($dbConnection, self::$user, self::$pwd);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }
}

?>
