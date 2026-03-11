<?php

require_once 'ManagerTrait.php';

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'admin') {
    echo "<div class='card' style='text-align: center; margin-top: 20px;'>";
    echo "<h2 style='color:red;'>🛑 Acesso Negado</h2>";
    echo "<p>Você não tem permissão para gerenciar usuários.</p>";
    echo "</div>";
    return;
}

class Usuarios
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
            <h2>Novo Usuário</h2>
            <form action="/financas/usuarios" method="POST" class='d-flex flex-column'>
                <div class='d-flex'>
                    <input type="text" name="nome" placeholder="Nome Completo" required style="flex-grow:1;">
                    <input type="email" name="email" placeholder="E-mail" required>
                </div>
                <div class='d-flex' style="margin-top:10px;">
                    <input type="password" name="senha" placeholder="Senha" required style="flex-grow:1;">
                    
                    <select name="perfil" required style="padding: 5px; margin-left: 5px;">
                        <option value="comum">Usuário Comum</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class='d-flex' style="margin-top:10px;">
                    <button type="submit"> Cadastrar Usuário </button>
                </div>
            </form>
<?php
        } else {
            echo "<h2>Gestão de Usuários</h2>";
            echo "<a href='/financas/usuarios?cadastrar'>[+ Novo Usuário]</a><br><br>";

            $sql = "SELECT * FROM usuarios";
            $consulta = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            echo count($consulta) == 0 ? "<p>Nenhum usuário encontrado.</p>" : null;

            echo "<table border='1' cellpadding='5' cellspacing='0' style='width:100%'>";
            // Adicionei a coluna 'Perfil' na tabela
            echo "<tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Cadastro</th><th>Ações</th></tr>";

            foreach ($consulta as $item) {
                $dataCadastro = date('d/m/Y H:i', strtotime($item['data_cadastro']));
                
                // Formatação visual para o perfil
                $badgePerfil = $item['perfil'] == 'admin' 
                    ? "<span style='background: #333; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.8em;'>Admin</span>" 
                    : "<span style='background: #ccc; padding: 2px 6px; border-radius: 4px; font-size: 0.8em;'>Comum</span>";

                echo "<tr>";
                echo "<td>{$item['id_usuario']}</td>";
                echo "<td>{$item['nome']}</td>";
                echo "<td>{$item['email']}</td>";
                echo "<td>{$badgePerfil}</td>";
                echo "<td>{$dataCadastro}</td>";
                echo "<td>";

                echo "<a href='/financas/usuarios/{$item['id_usuario']}?alterar'>[Editar]</a> ";
?>
                <form action="/financas/usuarios/<?= $item['id_usuario'] ?>" method="DELETE" style="display:inline;">
                    <button type="submit" style="color:red; font-size:0.8em;">X</button>
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
            $senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);

            $perfil = $dados['perfil'] ?? 'comum';

            $sql = "INSERT INTO usuarios (nome, email, senha, perfil, data_cadastro) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);

            $sucesso = $stmt->execute([
                $dados['nome'],
                $dados['email'],
                $senhaHash,
                $perfil,
            ]);

            if ($sucesso) {
                header("Location: /financas/usuarios");
                exit();
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "<p style='color:red'>Erro: Este e-mail já está cadastrado.</p>";
            } else {
                echo "<p style='color:red'>Erro ao cadastrar: " . $e->getMessage() . "</p>";
            }
        }
    }

    // GET com ID
    public function listarUm($id)
    {
        if (isset($_GET['alterar'])) {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
?>
            <h2>Editar Usuário</h2>
            <form action="/financas/usuarios/<?= $item['id_usuario'] ?>" method="PUT" class='d-flex flex-column' data-redirect="/financas/usuarios">
                <input type="text" name="nome" value="<?= $item['nome'] ?>" required>
                <input type="email" name="email" value="<?= $item['email'] ?>" required>

                <p style="font-size:0.8em; color:#666; margin-bottom: 5px;">Deixe a senha em branco para não alterar.</p>
                <div class='d-flex'>
                    <input type="password" name="senha" placeholder="Nova Senha (Opcional)" style="flex-grow:1;">
                    
                    <select name="perfil" required style="padding: 5px; margin-left: 5px;">
                        <option value="comum" <?= $item['perfil'] == 'comum' ? 'selected' : '' ?>>Usuário Comum</option>
                        <option value="admin" <?= $item['perfil'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>

                <div class='d-flex' style="margin-top:10px;">
                    <button type="submit"> Salvar Alterações </button>
                </div>
            </form>
<?php
        }
    }

    // PUT
    public function atualizar($id, $dados)
    {
        if (!empty($dados['senha'])) {
            $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome=?, email=?, senha=?, perfil=? WHERE id_usuario=?";
            $params = [$dados['nome'], $dados['email'], $senhaHash, $dados['perfil'], $id];
        } else {
            $sql = "UPDATE usuarios SET nome=?, email=?, perfil=? WHERE id_usuario=?";
            $params = [$dados['nome'], $dados['email'], $dados['perfil'], $id];
        }

        $stmt = $this->pdo->prepare($sql);
        echo $stmt->execute($params) ? "Usuário atualizado!" : "Erro ao atualizar.";
    }

    // DELETE
    public function deletar($id)
    {
        try {
            // Evita que o usuário logado exclua a própria conta
            if ($id == $_SESSION['id_usuario']) {
                http_response_code(403);
                echo "Você não pode excluir a sua própria conta.";
                return;
            }

            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id]);
            echo "Usuário removido!";
        } catch (PDOException $e) {
            http_response_code(400);
            echo "<p style='color:red'>Erro: Não é possível apagar este usuário pois ele tem dados (contas/metas) vinculados.</p>";
        }
    }
}

$usuario = new Usuarios($pdo);
$putData = isset($_PUT) ? $_PUT : [];
$usuario->processarRequisicao($metodo, $uri, $putData);

?>