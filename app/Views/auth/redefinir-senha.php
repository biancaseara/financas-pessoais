<div class="auth-wrapper">
    <div class="auth-card card" style="max-width: 420px;">

        <div class="auth-header">
            <div class="auth-logo-container">
                <img src="<?php echo BASE_URL; ?>/images/logo.png" alt="Ícone PREDITIV.IA" class="logo-img">
                <h1 class="logo-text">PREDITIV<span class="highlight">.IA</span></h1>
            </div>

            <h2 class="auth-title">Nova Senha</h2>
            <p class="text-secondary">Crie uma nova senha segura para o seu acesso.</p>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger">
                <i class="ph ph-warning-circle" style="font-size: 18px;"></i>
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success">
                <i class="ph ph-check-circle" style="font-size: 18px;"></i>
                <?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($token_valido): ?>
            <form action="/financas/auth/redefinirSenha" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group" style="margin-bottom: 0;">
                    <div class="input-with-icon">
                        <i class="ph ph-lock-key"></i>
                        <input type="password" name="senha" class="form-control" placeholder="Crie uma nova senha" required minlength="8" autocomplete="new-password">
                    </div>
                    <small class="text-secondary" style="font-size: 11px; margin-top: 4px; margin-left: 32px; display: block;">Mínimo 8 caracteres (letras e números).</small>
                </div>

                <div class="form-group" style="margin-bottom: 0; margin-top: 16px;">
                    <div class="input-with-icon">
                        <i class="ph ph-lock-key"></i>
                        <input type="password" name="senha_confirmacao" class="form-control" placeholder="Confirme a nova senha" required minlength="8" autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full" style="margin-top: 24px; height: 48px; font-size: 16px; justify-content: center;">
                    Salvar Senha <i class="ph ph-floppy-disk"></i>
                </button>
            </form>
        <?php endif; ?>

        <div class="auth-footer">
            <a href="/financas/auth/login" class="btn-outline w-full" style="display: flex; justify-content: center; align-items: center; text-decoration: none;">
                Voltar ao Login
            </a>
        </div>

    </div>
</div>