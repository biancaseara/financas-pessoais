<?php

require_once 'ManagerTrait.php';

class Transacoes
{

    use ManagerTrait;
    private $pdo;

    public function __construct($conexaoBanco)
    {
        $this->pdo = $conexaoBanco;
    }

    // GET
    public function listarTodos()
    {
        $contas = $this->pdo->query("SELECT * FROM contas")->fetchAll(PDO::FETCH_ASSOC);
        $categorias = $this->pdo->query("SELECT * FROM categorias ORDER BY tipo")->fetchAll(PDO::FETCH_ASSOC);

        if (isset($_GET['cadastrar'])) {
?>
            <h2>Nova Transação</h2>
            <form action="/financas/transacoes" method="POST" class='d-flex flex-column'>
                <div class='d-flex'>
                    <select name="id_conta" required style="padding: 5px; margin-right: 5px;">
                        <option value="" disabled selected>Escolha a Conta</option>
                        <?php foreach ($contas as $c): ?>
                            <option value="<?= $c['id_conta'] ?>">
                                <?= $c['nome_banco'] ?> (Saldo: R$ <?= $c['saldo_inicial'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="id_categoria" required style="padding: 5px; margin-right: 5px;">
                        <option value="" disabled selected>Escolha a Categoria</option>
                        <?php foreach ($categorias as $cat): ?>
                            <?php $tipo = ($cat['tipo'] == 'R') ? '[RECEITA]' : '[DESPESA]'; ?>
                            <option value="<?= $cat['id_categoria'] ?>">
                                <?= $tipo ?> <?= $cat['nome_categoria'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class='d-flex' style="margin-top: 10px;">
                    <input type="text" name="descricao" placeholder="Descrição (Ex: Compras do Mês)" required style="flex-grow: 1;">
                    <input type="number" step="0.01" name="valor" placeholder="Valor (R$)" required>
                    <input type="date" name="data_transacao" value="<?= date('Y-m-d') ?>" required>

                    <select name="tipo_transacao" required>
                        <option value="Saida">Saída (Gasto)</option>
                        <option value="Entrada">Entrada (Ganho)</option>
                    </select>
                </div>

                <div class='d-flex' style="margin-top: 10px;">
                    <button type="submit"> Registrar Transação </button>
                </div>
            </form>
            <?php
        } else {
            echo "<h2>Extrato de Transações</h2>";
            echo "<a href='/financas/transacoes?cadastrar'>[+ Nova Transação]</a><br><br>";

            $sql = "SELECT t.*, c.nome_banco, cat.nome_categoria 
                        FROM transacoes t 
                        JOIN contas c ON t.id_conta = c.id_conta 
                        JOIN categorias cat ON t.id_categoria = cat.id_categoria 
                        ORDER BY t.data_transacao DESC";

            $consulta = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            if (count($consulta) == 0) {
                echo "<p>Nenhuma transação registrada.</p>";
            }

            echo "<table border='1' cellpadding='5' cellspacing='0' style='width:100%'>";
            echo "<tr><th>Data</th><th>Conta</th><th>Categoria</th><th>Descrição</th><th>Valor</th><th>Ações</th></tr>";

            foreach ($consulta as $item) {
                $dataBr = date('d/m/Y', strtotime($item['data_transacao']));
                $corValor = ($item['tipo_transacao'] == 'Entrada') ? 'green' : 'red';

                echo "<tr>";
                echo "<td>{$dataBr}</td>";
                echo "<td>{$item['nome_banco']}</td>";
                echo "<td>{$item['nome_categoria']}</td>";
                echo "<td>{$item['descricao']}</td>";
                echo "<td style='color:$corValor; font-weight:bold;'>R$ {$item['valor']}</td>";
                echo "<td>";

                echo "<a href='/financas/transacoes/{$item['id_transacao']}?alterar' style='margin-right: 10px; display: inline;'>[Editar]</a>";
            ?>
                <form action="/financas/transacoes/<?= $item['id_transacao'] ?>" method="DELETE" style="display: inline">
                    <button type="submit" style="font-size: 0.8em; color: red;">X</button>
                </form>
            <?php
                echo "</td></tr>";
            }
            echo "</table>";
        }
    }

    // POST
    public function cadastrar($dados)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO transacoes (id_conta, id_categoria, descricao, valor, data_transacao, tipo_transacao) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $dados['id_conta'],
                $dados['id_categoria'],
                $dados['descricao'],
                $dados['valor'],
                $dados['data_transacao'],
                $dados['tipo_transacao']
            ]);

            $valor_ajuste = $_POST['valor'];
            if ($_POST['tipo_transacao'] == 'Saida') {
                $valor_ajuste = $valor_ajuste * -1;
            }

            $sqlSaldo = "UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?";
            $stmtSaldo = $this->pdo->prepare($sqlSaldo);
            $stmtSaldo->execute([$valor_ajuste, $dados['id_conta']]);

            $this->pdo->commit();

            echo "<p style='color:green'>Transação registrada e Saldo atualizado!</p> <a href='/financas/transacoes'>Voltar</a>";
        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo "<p style='color:red'>Erro ao registrar: " . $e->getMessage() . "</p>";
        }
    }

    // GET com ID
    public function listarUm($id)
    {
        if (isset($_GET['alterar'])) {
            // Busca a transação específica pelo ID
            $stmt = $this->pdo->prepare("SELECT * FROM transacoes WHERE id_transacao = ?");
            $stmt->execute([$id]);
            $transacao = $stmt->fetch(PDO::FETCH_ASSOC);

            // Busca as listas para os selects
            $contas = $this->pdo->query("SELECT * FROM contas")->fetchAll(PDO::FETCH_ASSOC);
            $categorias = $this->pdo->query("SELECT * FROM categorias ORDER BY tipo")->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <h2>Editar Transação</h2>
            <form action="/financas/transacoes/<?= $transacao['id_transacao'] ?>" method="PUT" class="d-flex flex-column" data-redirect="/financas/transacoes">
                <div class='d-flex'>
                    <select name="id_conta" required style="padding: 5px; margin-right: 5px;">
                        <?php foreach ($contas as $c): ?>
                            <option value="<?= $c['id_conta'] ?>" <?= ($c['id_conta'] == $transacao['id_conta']) ? 'selected' : '' ?>>
                                <?= $c['nome_banco'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="id_categoria" required style="padding: 5px; margin-right: 5px;">
                        <?php foreach ($categorias as $cat): ?>
                            <?php $tipo = ($cat['tipo'] == 'R') ? '[RECEITA]' : '[DESPESA]'; ?>
                            <option value="<?= $cat['id_categoria'] ?>" <?= ($cat['id_categoria'] == $transacao['id_categoria']) ? 'selected' : '' ?>>
                                <?= $tipo ?> <?= $cat['nome_categoria'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class='d-flex' style="margin-top: 10px;">
                    <input type="text" name="descricao" value="<?= $transacao['descricao'] ?>" required style="flex-grow: 1;">
                    <input type="number" step="0.01" name="valor" value="<?= $transacao['valor'] ?>" required>
                    <input type="date" name="data_transacao" value="<?= $transacao['data_transacao'] ?>" required>

                    <select name="tipo_transacao" required>
                        <option value="Saida" <?= ($transacao['tipo_transacao'] == 'Saida') ? 'selected' : '' ?>>Saída</option>
                        <option value="Entrada" <?= ($transacao['tipo_transacao'] == 'Entrada') ? 'selected' : '' ?>>Entrada</option>
                    </select>
                </div>

                <div class='d-flex' style="margin-top: 10px;">
                    <button type="submit"> Atualizar Transação </button>
                </div>
            </form>
<?php
        }
    }

    // PUT
    public function atualizar($id, $dados)
    {
        try {
            $this->pdo->beginTransaction();

            $stmtOld = $this->pdo->prepare("SELECT * FROM transacoes WHERE id_transacao = ?");
            $stmtOld->execute([$id]);
            $antiga = $stmtOld->fetch(PDO::FETCH_ASSOC);

            // Revertendo o saldo antigo
            $reverso = $antiga['valor'];
            if ($antiga['tipo_transacao'] == 'Entrada') {
                $reverso = $reverso * -1;
            }

            $sqlRevert = "UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?";
            $stmtRev = $this->pdo->prepare($sqlRevert);
            $stmtRev->execute([$reverso, $antiga['id_conta']]);

            // Atualizando transação
            $sqlUp = "UPDATE transacoes SET id_conta=?, id_categoria=?, descricao=?, valor=?, data_transacao=?, tipo_transacao=? WHERE id_transacao=?";
            $stmtUp = $this->pdo->prepare($sqlUp);
            $stmtUp->execute([
                $dados['id_conta'],
                $dados['id_categoria'],
                $dados['descricao'],
                $dados['valor'],
                $dados['data_transacao'],
                $dados['tipo_transacao'],
                $id,
            ]);

            // Aplicando novo saldo
            $novoValor = $dados['valor'];
            if ($dados['tipo_transacao'] == 'Saida') {
                $novoValor = $novoValor * -1;
            }

            $sqlAplica = "UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?";
            $stmtAplica = $this->pdo->prepare($sqlAplica);
            $stmtAplica->execute([$novoValor, $dados['id_conta']]);

            $this->pdo->commit();
            echo "Transação atualizada e saldos corrigidos!";
        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo "Erro ao atualizar: " . $e->getMessage();
        }
    }

    // DELETE
    public function deletar($id)
    {
        try {
            $this->pdo->beginTransaction();

            $stmtGet = $this->pdo->prepare("SELECT * FROM transacoes WHERE id_transacao = ?");
            $stmtGet->execute([$id]);
            $transacao = $stmtGet->fetch(PDO::FETCH_ASSOC);

            if ($transacao) {
                $valor = $transacao['valor'];
                $tipo = $transacao['tipo_transacao'];
                $id_conta = $transacao['id_conta'];

                // Ajuste reverso do saldo
                $tipo == 'Entrada' ? $ajuste = $valor * -1 : $ajuste = $valor;

                $stmtUpdate = $this->pdo->prepare("UPDATE contas SET saldo_inicial = saldo_inicial + ? WHERE id_conta = ?");
                $stmtUpdate->execute([$ajuste, $id_conta]);

                $stmtDelete = $this->pdo->prepare("DELETE FROM transacoes WHERE id_transacao = ?");

                $stmtDelete->execute([$id]);
                $this->pdo->commit();
                echo "<p style='color:green'>Transação excluída e Saldo atualizado!</p> <a href='/financas/transacoes'>Voltar</a>";
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo "<p style='color:red'>Erro ao excluir: " . $e->getMessage() . "</p>";
        }
    }
}

$transacao = new Transacoes($pdo);
$putData = isset($_PUT) ? $_PUT : [];
$transacao->processarRequisicao($metodo, $uri, $putData);

?>