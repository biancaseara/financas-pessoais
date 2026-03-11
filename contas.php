<?php

require_once 'ManagerTrait.php';

class Conta
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
        if (isset($_GET['cadastrar'])) {
?>
            <h2>Nova Conta</h2>
            <form action="/financas/contas" method="POST" class='d-flex flex-column'>
                <div class='d-flex'>
                    <input name='nome_banco' placeholder='Nome do Banco (Ex: Nubank)' required>
                    <input type="number" step="0.01" name='saldo_inicial' placeholder='Saldo Inicial' required>
                    <input type="color" name='cor_identificacao' value="#000000" title="Escolha a cor">
                </div>
                <div class='d-flex'>
                    <button type="submit"> Salvar Conta </button>
                </div>
            </form>
            <?php
        } else {
            echo "<h2>Minhas Contas</h2>";
            echo "<a href='/financas/contas?cadastrar'>[+ Nova Conta]</a><br><br>";

            $sql = "SELECT * FROM contas";
            $consulta = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($consulta as $item) {
                echo "<div style='border:1px solid #ccc; margin: 10px; padding: 10px;'>";
                echo "<b>Banco:</b> {$item['nome_banco']} <br>";
                echo "<b>Saldo:</b> R$ {$item['saldo_inicial']} <br>";
                echo "<b>Cor:</b> <span style='color:{$item['cor_identificacao']}'>████</span> <br>";

                echo "<a href='/financas/contas/{$item['id_conta']}?alterar'>[Editar]</a> ";
            ?>
                <form action="/financas/contas/<?= $item['id_conta'] ?>" method="DELETE" style="display:inline;">
                    <button type="submit"> Remover </button>
                </form>
                </div>
            <?php
            }
        }
    }

    // POST
    public function cadastrar($dados)
    {
        $sql = "INSERT INTO contas (id_usuario, nome_banco, saldo_inicial, cor_identificacao) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        $sucesso = $stmt->execute([
            $_SESSION['id_usuario'],
            $dados['nome_banco'],
            $dados['saldo_inicial'],
            $dados['cor_identificacao'],
        ]);

        echo $sucesso ? "<p style='color:green'>Conta criada com sucesso!</p> <a href='/financas/contas'>Voltar</a>" : "<p style='color:red'>Erro ao criar conta.</p>";
    }

    // GET com ID
    public function listarUm($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM contas WHERE id_conta = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($_GET['alterar'])) {
            ?>
            <h2>Editar Conta</h2>
            <form action="/financas/contas/<?= $item['id_conta'] ?>" method="PUT" class='d-flex flex-column' data-redirect="/financas/contas">
                <div class='d-flex'>
                    <input name='nome_banco' value='<?= $item['nome_banco'] ?>'>
                    <input name='saldo_inicial' value='<?= $item['saldo_inicial'] ?>'>
                    <input type="color" name='cor_identificacao' value='<?= $item['cor_identificacao'] ?>'>
                </div>
                <div class='d-flex'>
                    <button type="submit"> Salvar Alterações </button>
                </div>
            </form>
<?php
        }
    }

    // PUT
    public function atualizar($id, $dados)
    {
        $sql = "UPDATE contas SET nome_banco=?, saldo_inicial=?, cor_identificacao=? WHERE id_conta=?";
        $stmt = $this->pdo->prepare($sql);
        $sucesso = $stmt->execute([
            $dados['nome_banco'],
            $dados['saldo_inicial'],
            $dados['cor_identificacao'],
            $id
        ]);

        echo $sucesso ? "Conta atualizada!" : "Erro ao atualizar.";
    }

    public function deletar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM contas WHERE id_conta = ?");
        $sucesso = $stmt->execute([$id]);

        echo $sucesso ? "Conta removida!" : "Erro ao remover.";
    }
}

$conta = new Conta($pdo);
$putData = isset($_PUT) ? $_PUT : [];
$conta->processarRequisicao($metodo, $uri, $putData);

?>