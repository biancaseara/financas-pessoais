<?php

require_once 'ManagerTrait.php';

class Categoria
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
            <h2>Nova Categoria</h2>
            <form action="/financas/categorias" method="POST" class='d-flex flex-column'>
                <div class='d-flex'>
                    <input name='nome_categoria' placeholder='Nome da Categoria (Ex: Alimentação)' required>

                    <select name="tipo" required style="padding: 5px; margin-left: 5px;">
                        <option value="" disabled selected>Selecione o Tipo</option>
                        <option value="R">Receita (Entrada)</option>
                        <option value="D">Despesa (Saída)</option>
                    </select>
                </div>
                <div class='d-flex'>
                    <button type="submit"> Salvar Categoria </button>
                </div>
            </form>
            <?php
        } else {
            echo "<h2>Minhas Categorias</h2>";
            echo "<a href='/financas/categorias?cadastrar'>[+ Nova Categoria]</a><br><br>";

            $sql = "SELECT * FROM categorias ORDER BY tipo, nome_categoria";
            $consulta = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($consulta as $item) {
                $cor = ($item['tipo'] == 'R') ? 'green' : 'red';
                $labelTipo = ($item['tipo'] == 'R') ? 'RECEITA' : 'DESPESA';

                echo "<div style='border:1px solid #ccc; margin: 10px; padding: 10px; border-left: 5px solid $cor;'>";
                echo "<b>Categoria:</b> {$item['nome_categoria']} <br>";
                echo "<b>Tipo:</b> <span style='color:$cor; font-weight:bold;'>$labelTipo</span> <br>";

                echo "<a href='/financas/categorias/{$item['id_categoria']}?alterar'>[Editar]</a> ";
            ?>
                <form action="/financas/categorias/<?= $item['id_categoria'] ?>" method="DELETE" style="display:inline;">
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
        $sql = "INSERT INTO categorias (id_usuario, nome_categoria, tipo) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        $sucesso = $stmt->execute([
            $_SESSION['id_usuario'],
            $dados['nome_categoria'],
            $dados['tipo']
        ]);


        if ($sucesso) {
            header("Location: /financas/categorias");
            exit();
        } else {
            echo "<p style='color:red'>Erro ao criar categoria.</p>";
        }
    }

    // GET com ID
    public function listarUm($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($_GET['alterar'])) {
            ?>
            <h2>Editar Categoria</h2>
            <form action="/financas/categorias/<?= $item['id_categoria'] ?>" method="PUT" class='d-flex flex-column' data-redirect="/financas/categorias">
                <div class='d-flex'>
                    <input name='nome_categoria' value='<?= $item['nome_categoria'] ?>'>

                    <select name="tipo" required>
                        <option value="R" <?= $item['tipo'] == 'R' ? 'selected' : '' ?>>Receita</option>
                        <option value="D" <?= $item['tipo'] == 'D' ? 'selected' : '' ?>>Despesa</option>
                    </select>
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
        try {
            $sql = "UPDATE categorias SET nome_categoria=?, tipo=? WHERE id_categoria=?";
            $stmt = $this->pdo->prepare($sql);
            $sucesso = $stmt->execute([
                $dados['nome_categoria'],
                $dados['tipo'],
                $id,
            ]);

            echo $sucesso ? "Categoria atualizada!" : "Erro ao atualizar.";
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Falha: " . $e->getMessage();
        }
    }


    // DELETE
    public function deletar($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM categorias WHERE id_categoria = ?");
            $sucesso = $stmt->execute([$id]);

            echo $sucesso ? "Categoria removida!" : "Erro ao remover.";
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Falha: " . $e->getMessage();
        }
    }
}

$categoria = new Categoria($pdo);
$putData = isset($_PUT) ? $_PUT : [];
$categoria->processarRequisicao($metodo, $uri, $putData);

?>