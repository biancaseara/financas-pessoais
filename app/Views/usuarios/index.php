<div class="transactions-container">

    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-user-plus" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/usuarios/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <div class="input-with-icon">
                        <i class="ph ph-user"></i>
                        <input type="text" name="nome" class="form-control" placeholder="Nome do usuário" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <div class="input-with-icon">
                        <i class="ph ph-envelope-simple"></i>
                        <input type="email" name="email" class="form-control" placeholder="exemplo@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Senha de Acesso</label>
                    <div class="input-with-icon">
                        <i class="ph ph-lock-key"></i>
                        <input type="password" name="senha" class="form-control" placeholder="Defina uma senha" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Perfil de Acesso</label>
                    <div class="input-with-icon">
                        <i class="ph ph-shield-check"></i>
                        <select name="perfil" class="form-control" required>
                            <option value="comum">Usuário Comum</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-floppy-disk"></i> Cadastrar Usuário
                </button>
            </div>
        </form>
    </div>

    <div class="card table-container">
        <div class="card-header">
            <h4>Usuários do Sistema</h4>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($usuarios) > 0): ?>
                        <?php foreach ($usuarios as $item): ?>
                            <tr>
                                <td class="text-secondary font-medium">#<?= $item['id_usuario'] ?></td>
                                <td class="font-medium"><?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if ($item['perfil'] == 'admin'): ?>
                                        <span class="badge badge-admin"><i class="ph-fill ph-shield-star"></i> Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-common"><i class="ph ph-user"></i> Comum</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="/financas/usuarios/edit/<?= $item['id_usuario'] ?>" class="icon-btn-sm" title="Editar">
                                            <i class="ph ph-pencil-simple"></i>
                                        </a>
                                        
                                        <form action="/financas/usuarios/delete/<?= $item['id_usuario'] ?>" method="POST" style="margin: 0;">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="icon-btn-sm danger" title="Excluir" onclick="return confirm('Apagar este usuário? O processo é irreversível.');">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-secondary);">
                                <i class="ph ph-users" style="font-size: 32px; opacity: 0.5; margin-bottom: 8px; display: block;"></i>
                                Nenhum usuário encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>