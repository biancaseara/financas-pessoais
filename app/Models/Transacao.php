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
                LEFT JOIN contas c ON t.id_conta = c.id_conta 
                LEFT JOIN categorias cat ON t.id_categoria = cat.id_categoria 
                WHERE (c.id_usuario = ? OR t.id_fatura IN (SELECT id_fatura FROM faturas f JOIN cartoes car ON f.id_cartao = car.id_cartao WHERE car.id_usuario = ?))
                ORDER BY t.data_transacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario, $id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id, $id_usuario)
    {
        $sql = "SELECT t.* FROM transacoes t 
                LEFT JOIN contas c ON t.id_conta = c.id_conta 
                WHERE t.id_transacao = ? AND (c.id_usuario = ? OR t.id_fatura IS NOT NULL)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $id_usuario]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao, $id_conta_destino = null, $id_fatura = null)
    {
        if ($tipo_transacao == 'Transferencia' && empty($id_conta_destino)) {
            throw new Exception("Erro crítico: Conta de destino não informada para a transferência.");
        }

        try {
            $this->pdo->beginTransaction();

            // Insere a transação aceitando id_fatura
            $sql = "INSERT INTO transacoes (id_conta, id_categoria, descricao, valor, data_transacao, tipo_transacao, id_conta_destino, id_fatura) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao, $id_conta_destino, $id_fatura]);

            // Só mexe no saldo da conta se o $id_conta NÃO for nulo (ou seja, se for débito/dinheiro)
            if ($id_conta !== null) {
                if ($tipo_transacao == 'Transferencia') {
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial - ? WHERE id_conta = ?")->execute([$valor, $id_conta]);
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?")->execute([$valor, $id_conta_destino]);
                } else {
                    $valor_ajuste = ($tipo_transacao == 'Saida') ? ($valor * -1) : $valor;
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?")->execute([$valor_ajuste, $id_conta]);
                }
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
        try {
            $this->pdo->beginTransaction();

            $antiga = $this->buscarPorId($id, $id_usuario);
            if (!$antiga) {
                throw new Exception("Transação não encontrada ou acesso negado.");
            }

            // Só faz o recálculo de saldo se a transação for de uma Conta Bancária
            if ($antiga['id_conta'] !== null && $id_conta !== null) {
                
                // 1. REVERTER O SALDO ANTIGO
                if ($antiga['tipo_transacao'] == 'Transferencia') {
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?")->execute([$antiga['valor'], $antiga['id_conta']]);
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial - ? WHERE id_conta = ?")->execute([$antiga['valor'], $antiga['id_conta_destino']]);
                } else {
                    $reverso = ($antiga['tipo_transacao'] == 'Entrada') ? ($antiga['valor'] * -1) : $antiga['valor'];
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?")->execute([$reverso, $antiga['id_conta']]);
                }

                // 2. ATUALIZAR A TRANSAÇÃO NO BANCO
                $sqlUp = "UPDATE transacoes SET id_conta=?, id_categoria=?, descricao=?, valor=?, data_transacao=?, tipo_transacao=?, id_conta_destino=? WHERE id_transacao=?";
                $this->pdo->prepare($sqlUp)->execute([$id_conta, $id_categoria, $descricao, $valor, $data_transacao, $tipo_transacao, $id_conta_destino, $id]);

                // 3. APLICAR O NOVO SALDO
                if ($tipo_transacao == 'Transferencia') {
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial - ? WHERE id_conta = ?")->execute([$valor, $id_conta]);
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?")->execute([$valor, $id_conta_destino]);
                } else {
                    $novoValor = ($tipo_transacao == 'Saida') ? ($valor * -1) : $valor;
                    $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?")->execute([$novoValor, $id_conta]);
                }

            } else {
                // Se for cartão, apenas atualiza os dados visuais (sem mexer em saldo)
                $sqlUp = "UPDATE transacoes SET id_categoria=?, descricao=?, valor=?, data_transacao=? WHERE id_transacao=?";
                $this->pdo->prepare($sqlUp)->execute([$id_categoria, $descricao, $valor, $data_transacao, $id]);
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
                
                // Estorno só acontece se a transação for de Conta Corrente (não for cartão)
                if ($transacao['id_conta'] !== null) {
                    if ($transacao['tipo_transacao'] == 'Transferencia') {
                        $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?")->execute([$transacao['valor'], $transacao['id_conta']]);
                        $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial - ? WHERE id_conta = ?")->execute([$transacao['valor'], $transacao['id_conta_destino']]);
                    } else {
                        $ajuste = ($transacao['tipo_transacao'] == 'Entrada') ? ($transacao['valor'] * -1) : $transacao['valor'];
                        $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?")->execute([$ajuste, $transacao['id_conta']]);
                    }
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