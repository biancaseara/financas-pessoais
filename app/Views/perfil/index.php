<div class="transactions-container" style="align-items: flex-start;">
    
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success profile-alert">
            <i class="ph ph-check-circle" style="font-size: 18px;"></i> 
            Dados atualizados com sucesso!
        </div>
    <?php endif; ?>

    <div class="card form-container profile-card">
        <div class="card-header">
            <h4><i class="ph ph-user-gear" style="margin-right: 8px;"></i> <?= $titulo ?></h4>
        </div>

        <form action="/financas/perfil/update" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label>Nome Completo</label>
                <div class="input-with-icon">
                    <i class="ph ph-user"></i>
                    <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>E-mail de Acesso</label>
                <div class="input-with-icon">
                    <i class="ph ph-envelope-simple"></i>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                </div>
            </div>

            <hr class="form-divider">

            <div class="form-group">
                <label>Trocar Senha</label>
                <p class="text-secondary helper-text">Preencha apenas se quiser alterar a sua senha atual.</p>
                <div class="input-with-icon">
                    <i class="ph ph-lock-key"></i>
                    <input type="password" name="senha" class="form-control" placeholder="Digite a nova senha">
                </div>
            </div>

            <div class="form-actions" style="margin-top: 16px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-floppy-disk"></i> Atualizar Meus Dados
                </button>
            </div>
        </form>
    </div>
</div>