<h2><?= $titulo ?></h2>

<form action="/financas/usuarios/update/<?= $usuario['id_usuario'] ?>" method="POST" class="d-flex flex-column">
    <div class="d-flex">
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required style="flex-grow:1;">
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required style="flex-grow:1;">
    </div>

    <p style="font-size:0.8em; color:#666; margin-top: 10px; margin-bottom: 5px;">Deixe a senha em branco para não alterar.</p>
    <div class="d-flex">
        <input type="password" name="senha" placeholder="Nova Senha (Opcional)" style="flex-grow:1;">
        <select name="perfil" required style="padding: 5px; margin-left: 5px;">
            <option value="comum" <?= $usuario['perfil'] == 'comum' ? 'selected' : '' ?>>Usuário Comum</option>
            <option value="admin" <?= $usuario['perfil'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 20px;">
        <button type="submit">Salvar Alterações</button>
        <a href="/financas/usuarios" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>