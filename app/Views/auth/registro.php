<div class="auth-wrapper">
    <div class="auth-card card">

        <div class="auth-header">
            <div class="auth-logo-container">
                <img src="/financas/public/images/logo.png" alt="Ícone PREDITIV.IA" class="logo-img">
                <h1 class="logo-text">PREDITIV<span class="highlight">.IA</span></h1>
            </div>

            <h2 class="auth-title"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="text-secondary">Crie sua conta e comece a gerenciar suas finanças.</p>
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

        <form action="/financas/auth/registro" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <!-- Grid: Nome e E-mail lado a lado -->
            <div class="auth-form-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="input-with-icon">
                        <i class="ph ph-user"></i>
                        <input type="text" name="nome" class="form-control" placeholder="Seu Nome" required autocomplete="name">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <div class="input-with-icon">
                        <i class="ph ph-envelope-simple"></i>
                        <input type="email" name="email" class="form-control" placeholder="exemplo@email.com" required autocomplete="email">
                    </div>
                </div>
            </div>

            <!-- Grid: Senhas lado a lado -->
            <div class="auth-form-grid" style="margin-top: 4px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="input-with-icon">
                        <i class="ph ph-lock-key"></i>
                        <input type="password" name="senha" class="form-control" placeholder="Crie uma Senha" required minlength="8" autocomplete="new-password">
                    </div>
                    <small class="text-secondary" style="font-size: 11px; margin-top: 4px; margin-left: 32px; display: block;">Mínimo 8 caracteres (letras e números).</small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <div class="input-with-icon">
                        <i class="ph ph-lock-key"></i>
                        <input type="password" name="senha_confirmacao" class="form-control" placeholder="Confirme a Senha" required minlength="8" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="checkbox-group" style="margin-top: 8px;">
                <input type="checkbox" name="termos" id="termos" required>
                <label for="termos" class="text-secondary" style="font-size: 12px; margin-top: 0;">
                    Li e aceito os <a href="/financas/legal/termos" target="_blank" class="auth-link" style="margin-left: 0;">termos de uso</a> e a <a href="/financas/legal/privacidade" target="_blank" class="auth-link" style="margin-left: 0;">política de privacidade</a>.
                </label>
            </div>

            <button type="submit" class="btn-primary w-full" style="margin-top: 16px; height: 48px; font-size: 16px; justify-content: center;">
                Cadastrar <i class="ph ph-user-plus"></i>
            </button>
        </form>

        <div class="auth-footer">
            <span class="text-secondary">Já possui conta?</span>
            <a href="/financas/auth/login" class="auth-link">Faça Login</a>
        </div>

    </div>
</div>