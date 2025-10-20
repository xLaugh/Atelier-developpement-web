<?php
class Database {
    private $connection;

    public function __construct($settings) {
        $host = $settings['host'];
        $dbname = $settings['dbname'];
        $username = $settings['user'] ?? $settings['username'] ?? '';
        $password = $settings['pass'] ?? $settings['password'] ?? '';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        try {
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
            ]);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
