<?php

class Dashboard {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function getResumo($id_usuario) {
        // Total Recebido
        $stmt = $this->pdo->prepare("SELECT SUM(t.valor) as total FROM transacoes t JOIN contas c ON t.id_conta = c.id_conta WHERE t.tipo_transacao = 'Entrada' AND c.id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $entrada = $stmt->fetch()['total'] ?? 0;

        // Total Gasto (Sem acento em 'Saida')
        $stmt = $this->pdo->prepare("SELECT SUM(t.valor) as total FROM transacoes t JOIN contas c ON t.id_conta = c.id_conta WHERE t.tipo_transacao = 'Saida' AND c.id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $saida = $stmt->fetch()['total'] ?? 0;

        // Balanço Geral
        $stmt = $this->pdo->prepare("SELECT SUM(saldo_inicial) as total FROM contas WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $saldo = $stmt->fetch()['total'] ?? 0;

        return [
            'entrada' => $entrada,
            'saida' => $saida,
            'saldo' => $saldo
        ];
    }

    public function getRecentes($id_usuario) {
        $sql = "SELECT t.*, c.nome_banco, cat.nome_categoria 
                FROM transacoes t 
                JOIN contas c ON t.id_conta = c.id_conta 
                LEFT JOIN categorias cat ON t.id_categoria = cat.id_categoria 
                WHERE c.id_usuario = ?
                ORDER BY t.data_transacao DESC LIMIT 5";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }

    public function getOrcamentos($id_usuario) {
        $mesAtual = date('Y-m'); // Pega o ano e o mês atual (Ex: 2026-03)
        
        // Puxa as categorias que têm limite e soma os gastos delas neste mês
        $sql = "SELECT cat.nome_categoria, cat.limite_mensal, 
                       COALESCE(SUM(t.valor), 0) as total_gasto
                FROM categorias cat
                LEFT JOIN transacoes t ON cat.id_categoria = t.id_categoria 
                      AND t.tipo_transacao = 'Saida' 
                      AND DATE_FORMAT(t.data_transacao, '%Y-%m') = ?
                WHERE cat.id_usuario = ? AND cat.limite_mensal IS NOT NULL AND cat.limite_mensal > 0
                GROUP BY cat.id_categoria
                ORDER BY (COALESCE(SUM(t.valor), 0) / cat.limite_mensal) DESC";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$mesAtual, $id_usuario]);
        return $stmt->fetchAll();
    }

    public function getGastosPorCategoria($id_usuario) {
        $mesAtual = date('Y-m'); // Filtra apenas o mês atual
        
        $sql = "SELECT cat.nome_categoria, SUM(t.valor) as total
                FROM transacoes t
                JOIN categorias cat ON t.id_categoria = cat.id_categoria
                JOIN contas c ON t.id_conta = c.id_conta
                WHERE t.tipo_transacao = 'Saida' 
                  AND c.id_usuario = ? 
                  AND DATE_FORMAT(t.data_transacao, '%Y-%m') = ?
                GROUP BY cat.id_categoria
                ORDER BY total DESC";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario, $mesAtual]);
        return $stmt->fetchAll();
    }
}