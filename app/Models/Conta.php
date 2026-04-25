<?php

class Conta {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos($id_usuario) {
        $sql = "SELECT * FROM contas WHERE id_usuario = ? ORDER BY nome_banco";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id_conta, $id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM contas WHERE id_conta = ? AND id_usuario = ?");
        $stmt->execute([$id_conta, $id_usuario]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $nome_banco, $saldo_inicial, $cor) {
        $sql = "INSERT INTO contas (id_usuario, nome_banco, saldo_inicial, cor_identificacao) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $nome_banco, $saldo_inicial, $cor]);
    }

    public function atualizar($id_conta, $id_usuario, $nome_banco, $saldo_inicial, $cor) {
        $sql = "UPDATE contas SET nome_banco=?, saldo_inicial=?, cor_identificacao=? WHERE id_conta=? AND id_usuario=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nome_banco, $saldo_inicial, $cor, $id_conta, $id_usuario]);
    }

    public function deletar($id_conta, $id_usuario) {
        $stmt = $this->pdo->prepare("DELETE FROM contas WHERE id_conta = ? AND id_usuario = ?");
        return $stmt->execute([$id_conta, $id_usuario]);
    }

    public function obterPatrimonioTotal($id_usuario) {
        $sql = "SELECT SUM(saldo_inicial) as total FROM contas WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        $resultado = $stmt->fetch();
        return $resultado['total'] ?? 0;
    }
}