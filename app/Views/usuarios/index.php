<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/usuarios/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <div class="d-flex" style="gap: 10px;">
        <input type="text" name="nome" placeholder="Nome Completo" required style="flex-grow:1; padding: 10px;">
        <input type="email" name="email" placeholder="E-mail" required style="flex-grow:1; padding: 10px;">
    </div>
    <div class="d-flex" style="margin-top:10px; gap: 10px;">
        <input type="password" name="senha" placeholder="Senha" required style="flex-grow:1; padding: 10px;">
        <select name="perfil" required style="padding: 10px; flex-grow: 1;">
            <option value="comum">Usuário Comum</option>
            <option value="admin">Administrador</option>
        </select>
    </div>
    <div class="d-flex" style="margin-top:15px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer;">Cadastrar Usuário</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<table style="width: 100%; border-collapse: collapse; text-align: left;">
    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
        <th style="padding: 12px 8px;">ID</th>
        <th style="padding: 12px 8px;">Nome</th>
        <th style="padding: 12px 8px;">E-mail</th>
        <th style="padding: 12px 8px;">Perfil</th>
        <th style="padding: 12px 8px;">Ações</th>
    </tr>
    <?php foreach ($usuarios as $item): ?>
        <tr style="border-bottom: 1px solid #dee2e6;">
            <td style="padding: 12px 8px;"><?= $item['id_usuario'] ?></td>
            <td style="padding: 12px 8px;"><?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="padding: 12px 8px;"><?= htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="padding: 12px 8px;">
                <?php if ($item['perfil'] == 'admin'): ?>
                    <span style="background: #333; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.8em;">Admin</span>
                <?php else: ?>
                    <span style="background: #ccc; padding: 3px 8px; border-radius: 4px; font-size: 0.8em;">Comum</span>
                <?php endif; ?>
            </td>
            <td style="padding: 12px 8px; display: flex; gap: 5px;">
                <a href="/financas/usuarios/edit/<?= $item['id_usuario'] ?>" style="padding: 5px 10px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">Editar</a>
                
                <form action="/financas/usuarios/delete/<?= $item['id_usuario'] ?>" method="POST" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" style="background: #DC3545; color: white; padding: 5px 10px; font-size: 12px; border: none; cursor: pointer; border-radius: 4px;" onclick="return confirm('Apagar este usuário? O processo é irreversível.');">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>