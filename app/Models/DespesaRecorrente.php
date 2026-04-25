<?php

class DespesaRecorrente {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos($id_usuario) {
        $sql = "SELECT d.*, c.nome_banco, cat.nome_categoria 
                FROM despesas_recorrentes d
                JOIN contas c ON d.id_conta = c.id_conta
                JOIN categorias cat ON d.id_categoria = cat.id_categoria
                WHERE d.id_usuario = ? 
                ORDER BY d.status ASC, d.dia_vencimento ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id, $id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM despesas_recorrentes WHERE id_recorrente = ? AND id_usuario = ?");
        $stmt->execute([$id, $id_usuario]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $id_conta, $id_categoria, $descricao, $valor, $dia_vencimento) {
        $sql = "INSERT INTO despesas_recorrentes (id_usuario, id_conta, id_categoria, descricao, valor, dia_vencimento) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $id_conta, $id_categoria, $descricao, $valor, $dia_vencimento]);
    }

    public function atualizar($id, $id_usuario, $id_conta, $id_categoria, $descricao, $valor, $dia_vencimento, $status) {
        $sql = "UPDATE despesas_recorrentes SET id_conta=?, id_categoria=?, descricao=?, valor=?, dia_vencimento=?, status=? WHERE id_recorrente=? AND id_usuario=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_conta, $id_categoria, $descricao, $valor, $dia_vencimento, $status, $id, $id_usuario]);
    }

    public function deletar($id, $id_usuario) {
        $stmt = $this->pdo->prepare("DELETE FROM despesas_recorrentes WHERE id_recorrente = ? AND id_usuario = ?");
        return $stmt->execute([$id, $id_usuario]);
    }

    // A trava para evitar lançamentos duplicadas
    public function verificarLancamentoExistente($id_usuario, $descricao_formatada) {
        $sql = "SELECT id_transacao FROM transacoes t 
                LEFT JOIN contas c ON t.id_conta = c.id_conta
                WHERE c.id_usuario = ? AND t.descricao = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario, $descricao_formatada]);
        return $stmt->fetch() !== false;
    }
}