<?php

class Dashboard {
    private $pdo;

    public function __construct() {
        // Instancia a classe Database que criamos na pasta config
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function getResumo($id_usuario) {
        // Total Recebido
        $stmt = $this->pdo->prepare("SELECT SUM(t.valor) as total FROM transacoes t JOIN contas c ON t.id_conta = c.id_conta WHERE t.tipo_transacao = 'Entrada' AND c.id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $entrada = $stmt->fetch()['total'] ?? 0;

        // Total Gasto
        $stmt = $this->pdo->prepare("SELECT SUM(t.valor) as total FROM transacoes t JOIN contas c ON t.id_conta = c.id_conta WHERE t.tipo_transacao = 'Saida' AND c.id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $saida = $stmt->fetch()['total'] ?? 0;

        // Balanço Geral (Saldo inicial das contas)
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
                JOIN categorias cat ON t.id_categoria = cat.id_categoria 
                WHERE c.id_usuario = ?
                ORDER BY t.data_transacao DESC LIMIT 5";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }
}