<?php

class Transacao {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos() {
        $sql = "SELECT t.*, c.nome_banco, cat.nome_categoria 
                FROM transacoes t 
                JOIN contas c ON t.id_conta = c.id_conta 
                JOIN categorias cat ON t.id_categoria = cat.id_categoria 
                ORDER BY t.data_transacao DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM transacoes WHERE id_transacao = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function cadastrar($id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao) {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO transacoes (id_conta, id_categoria, descricao, valor, data_transacao, tipo_transacao) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao]);

            $valor_ajuste = ($tipo_transacao == 'Saida') ? ($valor * -1) : $valor;
            $sqlSaldo = "UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?";
            $stmtSaldo = $this->pdo->prepare($sqlSaldo);
            $stmtSaldo->execute([$valor_ajuste, $id_conta]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function atualizar($id, $id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao) {
        try {
            $this->pdo->beginTransaction();

            // 1. Reverter saldo antigo
            $antiga = $this->buscarPorId($id);
            $reverso = ($antiga['tipo_transacao'] == 'Entrada') ? ($antiga['valor'] * -1) : $antiga['valor'];
            
            $sqlRevert = "UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?";
            $stmtRev = $this->pdo->prepare($sqlRevert);
            $stmtRev->execute([$reverso, $antiga['id_conta']]);

            // 2. Atualizar a transação
            $sqlUp = "UPDATE transacoes SET id_conta=?, id_categoria=?, descricao=?, valor=?, data_transacao=?, tipo_transacao=? WHERE id_transacao=?";
            $stmtUp = $this->pdo->prepare($sqlUp);
            $stmtUp->execute([$id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao, $id]);

            // 3. Aplicar o novo saldo
            $novoValor = ($tipo_transacao == 'Saida') ? ($valor * -1) : $valor;
            $sqlAplica = "UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?";
            $stmtAplica = $this->pdo->prepare($sqlAplica);
            $stmtAplica->execute([$novoValor, $id_conta]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function deletar($id) {
        try {
            $this->pdo->beginTransaction();

            $transacao = $this->buscarPorId($id);
            if ($transacao) {
                $valor = $transacao['valor'];
                $tipo = $transacao['tipo_transacao'];
                $id_conta = $transacao['id_conta'];

                $ajuste = ($tipo == 'Entrada') ? ($valor * -1) : $valor;
                $stmtUpdate = $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?");
                $stmtUpdate->execute([$ajuste, $id_conta]);

                $stmtDelete = $this->pdo->prepare("DELETE FROM transacoes WHERE id_transacao = ?");
                $stmtDelete->execute([$id]);
                
                $this->pdo->commit();
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}