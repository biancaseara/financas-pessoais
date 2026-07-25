<?php

class MotorPreditivo {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function calcularProjecaoMensal($id_usuario) {
        $mesAtual = date('Y-m');
        $diaAtual = (int) date('d');
        $totalDiasMes = (int) date('t');

        $sql = "SELECT SUM(t.valor) as total_gasto 
                FROM transacoes t
                LEFT JOIN contas c ON t.id_conta = c.id_conta
                LEFT JOIN faturas f ON t.id_fatura = f.id_fatura
                LEFT JOIN cartoes car ON f.id_cartao = car.id_cartao
                WHERE (c.id_usuario = ? OR car.id_usuario = ?) 
                AND t.tipo_transacao = 'Saida' 
                AND DATE_FORMAT(t.data_transacao, '%Y-%m') = ?";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario, $id_usuario, $mesAtual]);
        $resultado = $stmt->fetch();
        
        $totalGasto = $resultado['total_gasto'] ?? 0;

        $burnRate = $diaAtual > 0 ? ($totalGasto / $diaAtual) : 0;
        $projecao = $burnRate * $totalDiasMes;

        return [
            'total_gasto_ate_agora' => (float) $totalGasto,
            'burn_rate_diario' => (float) $burnRate,
            'projecao_fim_mes' => (float) $projecao,
            'dias_restantes' => $totalDiasMes - $diaAtual
        ];
    }

    public function processarReservaAutomatica($id_usuario) {
        $sqlFixas = "SELECT SUM(valor) as total_fixo FROM despesas_recorrentes WHERE id_usuario = ? AND status = 'Ativo'";
        $stmtFixas = $this->pdo->prepare($sqlFixas);
        $stmtFixas->execute([$id_usuario]);
        $resultadoFixas = $stmtFixas->fetch();
        
        $custoFixoMensal = $resultadoFixas['total_fixo'] ?? 0;
        
        if ($custoFixoMensal <= 0) return false;

        $metaReservaIdeal = $custoFixoMensal * 6;

        $sqlCheck = "SELECT id_meta FROM metas WHERE id_usuario = ? AND titulo_meta LIKE '%Reserva%'";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id_usuario]);
        
        if (!$stmtCheck->fetch()) {
            $sqlInsert = "INSERT INTO metas (id_usuario, titulo_meta, valor_objetivo, valor_atual, data_limite) 
                          VALUES (?, 'Reserva de Emergência (Automática)', ?, 0, ?)";
            $dataLimite = date('Y-m-d', strtotime('+1 year')); 
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([$id_usuario, $metaReservaIdeal, $dataLimite]);
            return true;
        }
        
        return false;
    }

    public function encontrarRaloDinheiro($id_usuario) {
        $mesAtual = date('Y-m');
        
        $sql = "SELECT t.descricao, COUNT(*) as quantidade, SUM(t.valor) as total_gasto 
                FROM transacoes t
                LEFT JOIN contas c ON t.id_conta = c.id_conta
                LEFT JOIN faturas f ON t.id_fatura = f.id_fatura
                LEFT JOIN cartoes car ON f.id_cartao = car.id_cartao
                WHERE (c.id_usuario = ? OR car.id_usuario = ?) 
                AND t.tipo_transacao = 'Saida' 
                AND t.id_conta_destino IS NULL 
                AND DATE_FORMAT(t.data_transacao, '%Y-%m') = ?
                GROUP BY t.descricao 
                HAVING quantidade > 1 
                ORDER BY total_gasto DESC 
                LIMIT 3";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario, $id_usuario, $mesAtual]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function analisarContasEsquecidas($id_usuario) {
        $mesAtual = date('Y-m');
        $mesPassado = date('Y-m', strtotime('-1 month'));

        $sql = "SELECT t1.descricao, t1.valor 
                FROM transacoes t1
                LEFT JOIN contas c1 ON t1.id_conta = c1.id_conta
                LEFT JOIN faturas f1 ON t1.id_fatura = f1.id_fatura
                LEFT JOIN cartoes car1 ON f1.id_cartao = car1.id_cartao
                WHERE (c1.id_usuario = ? OR car1.id_usuario = ?) 
                AND t1.tipo_transacao = 'Saida' 
                AND DATE_FORMAT(t1.data_transacao, '%Y-%m') = ?
                AND t1.descricao NOT IN (
                    SELECT t2.descricao FROM transacoes t2 
                    LEFT JOIN contas c2 ON t2.id_conta = c2.id_conta
                    LEFT JOIN faturas f2 ON t2.id_fatura = f2.id_fatura
                    LEFT JOIN cartoes car2 ON f2.id_cartao = car2.id_cartao
                    WHERE (c2.id_usuario = ? OR car2.id_usuario = ?) 
                    AND DATE_FORMAT(t2.data_transacao, '%Y-%m') = ?
                )";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario, $id_usuario, $mesPassado, $id_usuario, $id_usuario, $mesAtual]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>