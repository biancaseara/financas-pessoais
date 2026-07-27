<div class="auth-wrapper">
    <div class="auth-card card" style="max-width: 420px;">

        <div class="auth-header">
            <div class="auth-logo-container">
                <img src="/images/logo.png" alt="Ícone PREDITIV.IA" class="logo-img">
                <h1 class="logo-text">PREDITIV<span class="highlight">.IA</span></h1>
            </div>

            <h2 class="auth-title">Recuperar Senha</h2>
            <p class="text-secondary">Digite seu e-mail para receber um link de redefinição seguro.</p>
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

        <form action="/financas/auth/recuperarSenha" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <div class="input-with-icon">
                    <i class="ph ph-envelope-simple"></i>
                    <input type="email" name="email" class="form-control" placeholder="exemplo@email.com" required autocomplete="email">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full" style="margin-top: 12px; height: 48px; font-size: 16px; justify-content: center;">
                Enviar Link <i class="ph ph-paper-plane-right"></i>
            </button>
        </form>

        <div class="auth-footer">
            <span class="text-secondary">Lembrou da senha?</span>
            <a href="/financas/auth/login" class="auth-link">Voltar ao Login</a>
        </div>

    </div>
</div>