<div class="auth-wrapper">
    <div class="auth-card card" style="max-width: 420px;">

        <div class="auth-header">
            <div class="auth-logo-container">
                <img src="<?php echo BASE_URL; ?>/images/logo.png" alt="Ícone PREDITIV.IA" class="logo-img">
                <h1 class="logo-text">PREDITIV<span class="highlight">.IA</span></h1>
            </div>

            <h2 class="auth-title"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="text-secondary">Acesse sua conta para continuar.</p>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger">
                <i class="ph ph-warning-circle" style="font-size: 18px;"></i>
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="/financas/auth/login" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <div class="input-with-icon">
                    <i class="ph ph-envelope-simple"></i>
                    <input type="email" name="email" class="form-control" placeholder="exemplo@email.com" required autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <div class="input-with-icon">
                    <i class="ph ph-lock-key"></i>
                    <input type="password" name="senha" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                </div>
                <div style="text-align: right; margin-top: 0; margin-bottom: 16px;">
                    <a href="/financas/auth/esqueciSenha" class="auth-link" style="font-size: 13px; margin: 0;">Esqueceu sua senha?</a>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full" style="margin-top: 12px; height: 48px; font-size: 16px; justify-content: center;">
                Entrar <i class="ph ph-sign-in"></i>
            </button>
        </form>

        <div class="auth-footer">
            <span class="text-secondary">Não tem uma conta?</span>
            <a href="/financas/auth/registro" class="auth-link">Crie aqui</a>
        </div>

    </div>
</div>