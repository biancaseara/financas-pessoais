<?php

require_once 'ManagerTrait.php';

class Metas
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
            <h2>Nova Meta Financeira</h2>
            <form action="/financas/metas" method="POST" class='d-flex flex-column'>
                <div class='d-flex'>
                    <input type="text" name="titulo_meta" placeholder="Objetivo (Ex: Comprar Carro)" required style="flex-grow:1;">
                    <input type="date" name="data_limite" required title="Data Limite">
                </div>
                <div class='d-flex' style="margin-top:10px;">
                    <input type="number" step="0.01" name="valor_objetivo" placeholder="Quanto precisa juntar? (R$)" required>
                    <input type="number" step="0.01" name="valor_atual" placeholder="Quanto já tem guardado? (R$)" required>
                </div>
                <div class='d-flex' style="margin-top:10px;">
                    <button type="submit"> Criar Meta </button>
                </div>
            </form>
            <?php
        } else {
            echo "<h2>Meus Objetivos</h2>";
            echo "<a href='/financas/metas?cadastrar'>[+ Nova Meta]</a><br><br>";

            $sql = "SELECT * FROM metas ORDER BY data_limite ASC";
            $consulta = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            if (count($consulta) == 0)
                echo "<p>Nenhuma meta definida ainda.</p>";

            foreach ($consulta as $item) {
                $objetivo = $item['valor_objetivo'];
                $atual = $item['valor_atual'];

                if ($objetivo > 0) {
                    $porcentagem = ($atual / $objetivo) * 100;
                } else {
                    $porcentagem = 0;
                }

                // Barra travada em 100%
                $larguraBarra = ($porcentagem > 100) ? 100 : $porcentagem;

                // Formata Data
                $dataBr = date('d/m/Y', strtotime($item['data_limite']));

                // HTML da Meta
                echo "<div style='border:1px solid #ccc; margin: 10px; padding: 15px; border-radius: 5px;'>";

                echo "<div style='display:flex; justify-content:space-between;'>";
                echo "<b>🎯 {$item['titulo_meta']}</b>";
                echo "<span style='color: #666;'>Meta: {$dataBr}</span>";
                echo "</div>";

                echo "<p>R$ {$atual} de R$ {$objetivo}</p>";

                // Barra de Progresso
                echo "<div style='background-color: #eee; width: 100%; height: 20px; border-radius: 10px; overflow: hidden; margin-bottom: 10px;'>";
                echo "<div style='background-color: #4CAF50; width: {$larguraBarra}%; height: 100%; text-align: center; color: white; font-size: 12px; line-height: 20px;'>";
                echo number_format($porcentagem, 1) . "%";
                echo "</div>";
                echo "</div>";

                // Botões
                echo "<a href='/financas/metas/{$item['id_meta']}?alterar'>[Editar Valor]</a> ";
            ?>
                <form action="/financas/metas/<?= $item['id_meta'] ?>" method="DELETE" style="display:inline;">
                    <button type="submit" style="color:red;">Desistir</button>
                </form>
                </div>
            <?php
            }
        }
    }

    // POST
    public function cadastrar($dados)
    {
        $sql = "INSERT INTO metas (id_usuario, titulo_meta, valor_objetivo, valor_atual, data_limite) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);


        $sucesso = $stmt->execute([
            $_SESSION['id_usuario'],
            $dados['titulo_meta'],
            $dados['valor_objetivo'],
            $dados['valor_atual'],
            $dados['data_limite']
        ]);

        echo $sucesso ? "<p style='color:green'>Meta criada!</p> <a href='/financas/metas'>Voltar</a>" : "<p style='color:red'>Erro ao criar meta.</p>";
    }

    // GET com ID
    public function listarUm($id)
    {
        if (isset($_GET['alterar'])) {
            $stmt = $this->pdo->prepare("SELECT * FROM metas WHERE id_meta = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <h2>Atualizar Meta</h2>
            <form action="/financas/metas/<?= $item['id_meta'] ?>" method="PUT" class='d-flex flex-column' data-redirect="/financas/metas">
                <p>Meta: <b><?= $item['titulo_meta'] ?></b></p>
                <label>Quanto você tem guardado agora?</label>
                <input type="number" step="0.01" name="valor_atual" value="<?= $item['valor_atual'] ?>">

                <label>Mudar Objetivo Total:</label>
                <input type="number" step="0.01" name="valor_objetivo" value="<?= $item['valor_objetivo'] ?>">

                <div class='d-flex' style="margin-top:10px;">
                    <button type="submit"> Atualizar Progresso </button>
                </div>
            </form>
<?php
        }
    }

    // PUT
    public function atualizar($id, $dados)
    {
        $sql = "UPDATE metas SET valor_atual=?, valor_objetivo=? WHERE id_meta=?";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$dados['valor_atual'], $dados['valor_objetivo'], $id])) {
            echo "Meta atualizada!";
        } else {
            echo "Erro ao atualizar.";
        }
    }

    // DELETE
    public function deletar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM metas WHERE id_meta = ?");
        echo $stmt->execute([$id]) ? "Meta removida!" : "Erro ao remover.";
    }
}

$meta = new Metas($pdo);
$putData = isset($_PUT) ? $_PUT : [];
$meta->processarRequisicao($metodo, $uri, $putData);
?>