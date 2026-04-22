<?php

class Transacao
{
    private $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos($id_usuario)
    {
        $sql = "SELECT t.*, c.nome_banco, cat.nome_categoria 
                FROM transacoes t 
                JOIN contas c ON t.id_conta = c.id_conta 
                LEFT JOIN categorias cat ON t.id_categoria = cat.id_categoria 
                WHERE c.id_usuario = ?
                ORDER BY t.data_transacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id, $id_usuario)
    {
        $sql = "SELECT t.* FROM transacoes t 
                JOIN contas c ON t.id_conta = c.id_conta 
                WHERE t.id_transacao = ? AND c.id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $id_usuario]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao, $id_conta_destino = null)
    {
        if ($tipo_transacao == 'Transferencia' && empty($id_conta_destino)) {
            throw new Exception("Erro crítico: Conta de destino não informada para a transferência.");
        }

        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO transacoes (id_conta, id_categoria, descricao, valor, data_transacao, tipo_transacao, id_conta_destino) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao, $id_conta_destino]);

            // Atualizando a coluna correta do BD: saldo_inicial
            if ($tipo_transacao == 'Transferencia') {
                $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial - ? WHERE id_conta = ? AND id_usuario = ?")->execute([$valor, $id_conta, $id_usuario]);
                $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ? AND id_usuario = ?")->execute([$valor, $id_conta_destino, $id_usuario]);
            } else {
                $valor_ajuste = ($tipo_transacao == 'Saida') ? ($valor * -1) : $valor;
                $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ? AND id_usuario = ?")->execute([$valor_ajuste, $id_conta, $id_usuario]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro ao processar transação: " . $e->getMessage());
        }
    }

    public function atualizar($id, $id_usuario, $id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao, $id_conta_destino = null)
    {
        if ($tipo_transacao == 'Transferencia' && empty($id_conta_destino)) {
            throw new Exception("Erro crítico: Conta de destino não informada.");
        }

        try {
            $this->pdo->beginTransaction();

            $antiga = $this->buscarPorId($id, $id_usuario);
            if (!$antiga) {
                throw new Exception("Transação não encontrada ou acesso negado.");
            }
            
            $isTransferenciaAntiga = ($antiga['tipo_transacao'] == 'Transferencia' || !empty($antiga['id_conta_destino']));

            // 1. REVERTER O SALDO ANTIGO
            if ($isTransferenciaAntiga) {
                $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ? AND id_usuario = ?")->execute([$antiga['valor'], $antiga['id_conta'], $id_usuario]);
                $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial - ? WHERE id_conta = ? AND id_usuario = ?")->execute([$antiga['valor'], $antiga['id_conta_destino'], $id_usuario]);
            } else {
                $reverso = ($antiga['tipo_transacao'] == 'Entrada') ? ($antiga['valor'] * -1) : $antiga['valor'];
                $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ? AND id_usuario = ?")->execute([$reverso, $antiga['id_conta'], $id_usuario]);
            }

            // 2. ATUALIZAR A TRANSAÇÃO NO BANCO
            $sqlUp = "UPDATE transacoes SET id_conta=?, id_categoria=?, descricao=?, valor=?, data_transacao=?, tipo_transacao=?, id_conta_destino=? WHERE id_transacao=?";
            $this->pdo->prepare($sqlUp)->execute([$id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao, $id_conta_destino, $id]);

            // 3. APLICAR O NOVO SALDO
            $isTransferenciaNova = ($tipo_transacao == 'Transferencia' || !empty($id_conta_destino));

            if ($isTransferenciaNova) {
                $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial - ? WHERE id_conta = ? AND id_usuario = ?")->execute([$valor, $id_conta, $id_usuario]);
                $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ? AND id_usuario = ?")->execute([$valor, $id_conta_destino, $id_usuario]);
            } else {
                $novoValor = ($tipo_transacao == 'Saida') ? ($valor * -1) : $valor;
                $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ? AND id_usuario = ?")->execute([$novoValor, $id_conta, $id_usuario]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro ao atualizar transação: " . $e->getMessage());
        }
    }

    public function deletar($id, $id_usuario)
    {
        try {
            $this->pdo->beginTransaction();

            $transacao = $this->buscarPorId($id, $id_usuario);
            if ($transacao) {
                $isTransferencia = ($transacao['tipo_transacao'] == 'Transferencia' || !empty($transacao['id_conta_destino']));

                // Estorno Matemático
                if ($isTransferencia) {
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ? AND id_usuario = ?")->execute([$transacao['valor'], $transacao['id_conta'], $id_usuario]);
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial - ? WHERE id_conta = ? AND id_usuario = ?")->execute([$transacao['valor'], $transacao['id_conta_destino'], $id_usuario]);
                } else {
                    $ajuste = ($transacao['tipo_transacao'] == 'Entrada') ? ($transacao['valor'] * -1) : $transacao['valor'];
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ? AND id_usuario = ?")->execute([$ajuste, $transacao['id_conta'], $id_usuario]);
                }

                $this->pdo->prepare("DELETE FROM transacoes WHERE id_transacao = ?")->execute([$id]);
                $this->pdo->commit();
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro ao excluir transação: " . $e->getMessage());
        }
    }
}