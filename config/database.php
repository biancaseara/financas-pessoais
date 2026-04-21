<?php

class Database {
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $password;
    private $pdo;

    public function getConnection() {
        $this->pdo = null;

        $this->host = $_ENV['DB_HOST'];
        $this->port = $_ENV['DB_PORT'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->user = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];

        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->user, $this->password);
           
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Erro de Conexão: " . $e->getMessage());
        }

        return $this->pdo;
    }
}
?>