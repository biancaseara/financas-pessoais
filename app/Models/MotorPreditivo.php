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

    public function analisarExtratoTexto($textoBruto) {
        $apiKey =$_ENV['GEMINI_API_KEY'];
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

        $prompt = "Você é um assistente financeiro especialista em processamento de dados.
        Vou te enviar um texto bruto copiado de um extrato bancário brasileiro.
        Sua única tarefa é extrair as movimentações reais e retornar ESTRITAMENTE um array JSON válido, sem NENHUM texto adicional antes ou depois, e sem marcação markdown (não use ```json).
        
        Regras de extração:
        1. ANONIMIZAÇÃO RIGOROSA: Ignore e descarte completamente qualquer informação de identificação pessoal do titular da conta presente no texto, como nomes próprios, CPFs (mesmo mascarados como ***.123.456-**), RGs ou endereços.
        2. Ignore saldos iniciais, saldos finais, rendimento líquido, totais do período e textos padrão do banco.
        3. 'data': Formato YYYY-MM-DD. Se a linha da transação não tiver ano, busque o ano no cabeçalho do texto. Converta meses em português (ex: JUN para 06).
        4. 'descricao': O nome do estabelecimento limpo. Remova CNPJs, códigos inúteis de agência/conta e textos como 'Transferência enviada pelo Pix'.
        5. 'valor': Apenas numérico positivo (float com ponto, ex: 12.90).
        6. 'tipo_transacao': Retorne 'Saida' (para valores negativos, pagamentos ou débitos) ou 'Entrada' (para valores positivos, rendimentos ou recebimentos).
        7. 'forma_pagamento': Deduza pelo texto (ex: 'Pix', 'Crédito', 'Débito', 'Transferência').
        8. 'categoria': Atribua uma categoria básica lógica (Alimentação, Moradia, Transporte, Saúde, Educação, Lazer, Renda Principal, Outros).
        9. 'parcelas': Se o texto indicar parcelamento (ex: 01/05 ou 2/12), retorne uma string '1/5'. Se não houver, retorne null.

        Texto do Extrato:
        " . $textoBruto;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.1
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        $resultado = json_decode($response, true);

        if (isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
            $textoIA = trim($resultado['candidates'][0]['content']['parts'][0]['text']);
            
            $textoIA = str_replace(['```json', '```'], '', $textoIA);
            
            return trim($textoIA);
        }

        return "[]"; 
    }
}
?>