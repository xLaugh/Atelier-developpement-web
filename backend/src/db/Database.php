<?php
class Database {
    private $connection;

    public function __construct($settings) {
        $host = $settings['host'];
        $dbname = $settings['dbname'];
        $username = $settings['username'];
        $password = $settings['password'];

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

        try {
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
