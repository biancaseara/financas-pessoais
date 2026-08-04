<?php

class Cartao {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }
    
    public function listarTodos($id_usuario) {
        $sql = "SELECT * FROM cartoes WHERE id_usuario = ? ORDER BY nome_cartao";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id_cartao, $id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM cartoes WHERE id_cartao = ? AND id_usuario = ?");
        $stmt->execute([$id_cartao, $id_usuario]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $nome_cartao, $limite_total, $dia_fechamento, $dia_vencimento, $cor_identificacao) {
        $sql = "INSERT INTO cartoes (id_usuario, nome_cartao, limite_total, dia_fechamento, dia_vencimento, cor_identificacao) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $nome_cartao, $limite_total, $dia_fechamento, $dia_vencimento, $cor_identificacao]);
    }

    public function atualizar($id_cartao, $id_usuario, $nome_cartao, $limite_total, $dia_fechamento, $dia_vencimento, $cor_identificacao) {
        $sql = "UPDATE cartoes SET nome_cartao=?, limite_total=?, dia_fechamento=?, dia_vencimento=?, cor_identificacao=? WHERE id_cartao=? AND id_usuario=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nome_cartao, $limite_total, $dia_fechamento, $dia_vencimento, $cor_identificacao, $id_cartao, $id_usuario]);
    }

    public function deletar($id_cartao, $id_usuario) {
        $stmt = $this->pdo->prepare("DELETE FROM cartoes WHERE id_cartao = ? AND id_usuario = ?");
        return $stmt->execute([$id_cartao, $id_usuario]);
    }
    
    public function obterValorFaturaAtual($id_cartao) {
        $mesAno = date('Y-m'); 
        $sql = "SELECT id_fatura, valor_total, status FROM faturas WHERE id_cartao = ? AND mes_ano = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_cartao, $mesAno]);
        return $stmt->fetch() ?: ['id_fatura' => null, 'valor_total' => 0, 'status' => 'Sem Fatura'];
    }

    public function calcularLimiteDisponivel($limite_total, $id_cartao) {
        $sql = "SELECT SUM(valor_total) as limite_usado FROM faturas WHERE id_cartao = ? AND status = 'Aberta'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_cartao]);
        $resultado = $stmt->fetch();
        
        $limite_usado = $resultado['limite_usado'] ?? 0;
        return max(0, $limite_total - $limite_usado);
    }
}
?>