<?php

class Dashboard {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function getResumo($id_usuario) {
        $mesAtual = date('Y-m');

        // 1. Patrimônio Líquido (Soma de TODO o dinheiro em contas, não importa a data)
        $stmtPatrimonio = $this->pdo->prepare("SELECT SUM(saldo_inicial) as total FROM contas WHERE id_usuario = ?");
        $stmtPatrimonio->execute([$id_usuario]);
        $patrimonio = $stmtPatrimonio->fetch()['total'] ?? 0;

        // 2. Receitas do Mês Atual
        $sqlReceitas = "SELECT SUM(t.valor) as total 
                        FROM transacoes t 
                        LEFT JOIN contas c ON t.id_conta = c.id_conta 
                        WHERE c.id_usuario = ? 
                        AND t.tipo_transacao = 'Entrada' 
                        AND t.data_transacao LIKE ?";
        $stmtReceitas = $this->pdo->prepare($sqlReceitas);
        $stmtReceitas->execute([$id_usuario, $mesAtual . '-%']);
        $entrada = $stmtReceitas->fetch()['total'] ?? 0;

        // 3. Despesas do Mês Atual (Conta + Cartão de Crédito juntos)
        $sqlDespesas = "SELECT SUM(t.valor) as total 
                        FROM transacoes t 
                        LEFT JOIN contas c ON t.id_conta = c.id_conta 
                        LEFT JOIN faturas f ON t.id_fatura = f.id_fatura 
                        LEFT JOIN cartoes car ON f.id_cartao = car.id_cartao 
                        WHERE (c.id_usuario = ? OR car.id_usuario = ?) 
                        AND t.tipo_transacao = 'Saida' 
                        AND t.data_transacao LIKE ?";
        $stmtDespesas = $this->pdo->prepare($sqlDespesas);
        $stmtDespesas->execute([$id_usuario, $id_usuario, $mesAtual . '-%']);
        $saida = $stmtDespesas->fetch()['total'] ?? 0;

        return [
            'entrada' => $entrada,
            'saida' => $saida,
            'patrimonio' => $patrimonio,
            'balanco' => $entrada - $saida
        ];
    }

    public function getRecentes($id_usuario) {
        $sql = "SELECT t.*, c.nome_banco, cat.nome_categoria 
                FROM transacoes t 
                LEFT JOIN contas c ON t.id_conta = c.id_conta 
                LEFT JOIN categorias cat ON t.id_categoria = cat.id_categoria 
                WHERE (c.id_usuario = ? OR t.id_fatura IN (SELECT id_fatura FROM faturas f JOIN cartoes car ON f.id_cartao = car.id_cartao WHERE car.id_usuario = ?))
                ORDER BY t.data_transacao DESC LIMIT 5";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario, $id_usuario]);
        return $stmt->fetchAll();
    }

    public function getOrcamentos($id_usuario) {
        $mesAtual = date('Y-m'); 
        
        // Inclusão de gastos de cartão no limite da categoria
        $sql = "SELECT cat.nome_categoria, cat.limite_mensal, 
                       COALESCE(SUM(t.valor), 0) as total_gasto
                FROM categorias cat
                LEFT JOIN transacoes t ON cat.id_categoria = t.id_categoria 
                      AND t.tipo_transacao = 'Saida' 
                      AND DATE_FORMAT(t.data_transacao, '%Y-%m') = ?
                      AND (t.id_conta IN (SELECT id_conta FROM contas WHERE id_usuario = ?) OR t.id_fatura IN (SELECT id_fatura FROM faturas f JOIN cartoes car ON f.id_cartao = car.id_cartao WHERE car.id_usuario = ?))
                WHERE cat.id_usuario = ? AND cat.limite_mensal IS NOT NULL AND cat.limite_mensal > 0
                GROUP BY cat.id_categoria
                ORDER BY (COALESCE(SUM(t.valor), 0) / cat.limite_mensal) DESC";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$mesAtual, $id_usuario, $id_usuario, $id_usuario]);
        return $stmt->fetchAll();
    }

    public function getGastosPorCategoria($id_usuario) {
        $mesAtual = date('Y-m');
        
        $sql = "SELECT cat.nome_categoria, SUM(t.valor) as total
                FROM transacoes t
                JOIN categorias cat ON t.id_categoria = cat.id_categoria
                LEFT JOIN contas c ON t.id_conta = c.id_conta
                LEFT JOIN faturas f ON t.id_fatura = f.id_fatura
                LEFT JOIN cartoes car ON f.id_cartao = car.id_cartao
                WHERE t.tipo_transacao = 'Saida' 
                  AND (c.id_usuario = ? OR car.id_usuario = ?)
                  AND DATE_FORMAT(t.data_transacao, '%Y-%m') = ?
                GROUP BY cat.id_categoria
                ORDER BY total DESC";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario, $id_usuario, $mesAtual]);
        return $stmt->fetchAll();
    }
}