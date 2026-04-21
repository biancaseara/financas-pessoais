<h2><?= $titulo ?></h2>

<form action="/financas/usuarios/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <div class="d-flex">
        <input type="text" name="nome" placeholder="Nome Completo" required style="flex-grow:1;">
        <input type="email" name="email" placeholder="E-mail" required>
    </div>
    <div class="d-flex" style="margin-top:10px;">
        <input type="password" name="senha" placeholder="Senha" required style="flex-grow:1;">
        <select name="perfil" required style="padding: 5px; margin-left: 5px;">
            <option value="comum">Usuário Comum</option>
            <option value="admin">Administrador</option>
        </select>
    </div>
    <div class="d-flex" style="margin-top:10px;">
        <button type="submit">Cadastrar Usuário</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<table>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>E-mail</th>
        <th>Perfil</th>
        <th>Ações</th>
    </tr>
    <?php foreach ($usuarios as $item): ?>
        <tr>
            <td><?= $item['id_usuario'] ?></td>
            <td><?= htmlspecialchars($item['nome']) ?></td>
            <td><?= htmlspecialchars($item['email']) ?></td>
            <td>
                <?php if ($item['perfil'] == 'admin'): ?>
                    <span style="background: #333; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.8em;">Admin</span>
                <?php else: ?>
                    <span style="background: #ccc; padding: 3px 8px; border-radius: 4px; font-size: 0.8em;">Comum</span>
                <?php endif; ?>
            </td>
            <td style="display: flex; gap: 5px;">
                <a href="/financas/usuarios/edit/<?= $item['id_usuario'] ?>" style="padding: 5px 10px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">Editar</a>
                <form action="/financas/usuarios/delete/<?= $item['id_usuario'] ?>" method="POST" style="margin: 0;">
                    <button type="submit" style="background: #DC3545; padding: 5px 10px; font-size: 12px; border: none; cursor: pointer;" onclick="return confirm('Apagar este usuário?');">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>