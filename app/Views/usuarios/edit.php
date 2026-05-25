<div class="transactions-container">
    <div class="card form-container">
        <div class="card-header">
            <h4><i class="ph ph-pencil-simple" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/usuarios/update/<?= $usuario['id_usuario'] ?>" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <div class="input-with-icon">
                        <i class="ph ph-user"></i>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <div class="input-with-icon">
                        <i class="ph ph-envelope-simple"></i>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nova Senha</label>
                    <p class="text-secondary helper-text">Deixe em branco para manter a senha atual.</p>
                    <div class="input-with-icon">
                        <i class="ph ph-lock-key"></i>
                        <input type="password" name="senha" class="form-control" placeholder="Nova Senha (Opcional)">
                    </div>
                </div>

                <div class="form-group">
                    <label>Perfil de Acesso</label>
                    <div class="input-with-icon">
                        <i class="ph ph-shield-check"></i>
                        <select name="perfil" class="form-control" required>
                            <option value="comum" <?= $usuario['perfil'] == 'comum' ? 'selected' : '' ?>>Usuário Comum</option>
                            <option value="admin" <?= $usuario['perfil'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px; display: flex; gap: 16px;">
                <a href="/financas/usuarios" class="btn-outline flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-check-circle"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>