<?php

class Fatura {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function ListarPorCartao($id_cartao, $id_usuario) {
        $sql = "SELECT f.* FROM faturas f 
                JOIN cartoes c ON f.id_cartao = c.id_cartao 
                WHERE f.id_cartao = ? AND c.id_usuario = ? 
                ORDER BY f.mes_ano DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_cartao, $id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id_fatura, $id_usuario) {
        $sql = "SELECT f.*, c.nome_cartao FROM faturas f 
                JOIN cartoes c ON f.id_cartao = c.id_cartao 
                WHERE f.id_fatura = ? AND c.id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_fatura, $id_usuario]);
        return $stmt->fetch();
    }

    // Calcula o valor total sumando as transações e atualiza a tabela
    public function atualizarValorTotal($id_fatura) {
        $sql = "UPDATE faturas 
                SET valor_total = (SELECT COALESCE(SUM(valor), 0) FROM transacoes WHERE id_fatura = ?)
                WHERE id_fatura = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_fatura, $id_fatura]);
    }

    // Se a fatura do mês não existir na hora da compra, o sistema cria sozinha
    public function buscarOuCriarAberta($id_cartao, $mes_ano) {
        $stmt = $this->pdo->prepare("SELECT id_fatura FROM faturas WHERE id_cartao = ? AND mes_ano = ?");
        $stmt->execute([$id_cartao, $mes_ano]);
        $fatura = $stmt->fetch();

        if ($fatura) {
            return $fatura['id_fatura'];
        }

        // Se não achou, insere uma nova
        $sqlInsert = "INSERT INTO faturas (id_cartao, mes_ano, status) VALUES (?, ?, 'Aberta')";
        $this->pdo->prepare($sqlInsert)->execute([$id_cartao, $mes_ano]);
        return $this->pdo->lastInsertId();
    }

    public function mudarStatus($id_fatura, $id_usuario, $novo_status) {
        $sql = "UPDATE faturas f 
                JOIN cartoes c ON f.id_cartao = c.id_cartao 
                SET f.status = ? 
                WHERE f.id_fatura = ? AND c.id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$novo_status, $id_fatura, $id_usuario]);
    }
}

?>