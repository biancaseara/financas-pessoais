<?php

class Database {
    private $host = 'localhost';
    private $port = '3306';
    private $dbname = 'NOME_DO_SEU_BANCO';
    private $user = 'SEU_USUARIO';
    private $password = 'SUA_SENHA';
    private $pdo;

    public function getConnection() {
        $this->pdo = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->user, $this->password);
            // Configuração do PDO para lançar exceções em caso de erro
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Retorna dados como array associativo por padrão
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Erro de Conexão: " . $e->getMessage());
        }

        return $this->pdo;
    }
}
?>