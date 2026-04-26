<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/usuarios/update/<?= $usuario['id_usuario'] ?>" method="POST" class="d-flex flex-column">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <div class="d-flex" style="gap: 10px;">
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>" required style="flex-grow:1; padding: 10px;">
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>" required style="flex-grow:1; padding: 10px;">
    </div>

    <p style="font-size:0.85em; color:#666; margin-top: 15px; margin-bottom: 5px;">Deixe a senha em branco para não alterar.</p>
    <div class="d-flex" style="gap: 10px;">
        <input type="password" name="senha" placeholder="Nova Senha (Opcional)" style="flex-grow:1; padding: 10px;">
        <select name="perfil" required style="padding: 10px; flex-grow:1;">
            <option value="comum" <?= $usuario['perfil'] == 'comum' ? 'selected' : '' ?>>Usuário Comum</option>
            <option value="admin" <?= $usuario['perfil'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 25px; gap: 10px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer; background-color: #007BFF; color: white; border: none; border-radius: 4px;">Salvar Alterações</button>
        <a href="/financas/usuarios" style="padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>