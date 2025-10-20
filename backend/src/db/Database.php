<?php
class Database {
    private $pdo;

    public function __construct($settings) {
        $dsn = "mysql:host={$settings['host']};dbname={$settings['dbname']};charset=utf8";
        $this->pdo = new PDO($dsn, $settings['user'], $settings['pass']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection() {
        return $this->pdo;
    }
}
