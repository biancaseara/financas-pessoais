<?php

class ConselhoIa {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function salvarConselho($id_usuario, $mensagem, $tipo = 'geral') {
        $sql = "INSERT INTO conselhos_ia (id_usuario, mensagem, tipo) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $mensagem, $tipo]);
    }

    public function buscarUltimoConselho($id_usuario) {
        $sql = "SELECT * FROM conselhos_ia WHERE id_usuario = ? ORDER BY data_criacao DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarUltimosConselhos($id_usuario, $limite = 3) {
        $sql = "SELECT * FROM conselhos_ia WHERE id_usuario = ? ORDER BY data_criacao DESC LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limite, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>