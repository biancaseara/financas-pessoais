<?php

class Investimento {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos($id_usuario) {
        $sql = "SELECT * FROM investimentos WHERE id_usuario = ? ORDER BY data_aplicacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id, $id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM investimentos WHERE id_investimento = ? AND id_usuario = ?");
        $stmt->execute([$id, $id_usuario]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $nome, $tipo, $corretora, $valor, $data_aplicacao, $vencimento) {
        $sql = "INSERT INTO investimentos (id_usuario, nome_investimento, tipo, corretora, valor_aplicado, data_aplicacao, vencimento) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $nome, $tipo, $corretora, $valor, $data_aplicacao, $vencimento]);
    }

    public function atualizar($id, $id_usuario, $nome, $tipo, $corretora, $valor, $data_aplicacao, $vencimento) {
        $sql = "UPDATE investimentos SET nome_investimento=?, tipo=?, corretora=?, valor_aplicado=?, data_aplicacao=?, vencimento=? WHERE id_investimento=? AND id_usuario=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nome, $tipo, $corretora, $valor, $data_aplicacao, $vencimento, $id, $id_usuario]);
    }

    public function deletar($id, $id_usuario) {
        $stmt = $this->pdo->prepare("DELETE FROM investimentos WHERE id_investimento = ? AND id_usuario = ?");
        return $stmt->execute([$id, $id_usuario]);
    }
}