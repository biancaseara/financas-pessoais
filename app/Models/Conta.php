<?php

class Conta {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos() {
        $sql = "SELECT * FROM contas ORDER BY nome_banco";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM contas WHERE id_conta = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $nome_banco, $saldo_inicial, $cor) {
        $sql = "INSERT INTO contas (id_usuario, nome_banco, saldo_inicial, cor_identificacao) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $nome_banco, $saldo_inicial, $cor]);
    }

    public function atualizar($id, $nome_banco, $saldo_inicial, $cor) {
        $sql = "UPDATE contas SET nome_banco=?, saldo_inicial=?, cor_identificacao=? WHERE id_conta=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nome_banco, $saldo_inicial, $cor, $id]);
    }

    public function deletar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM contas WHERE id_conta = ?");
        return $stmt->execute([$id]);
    }
}