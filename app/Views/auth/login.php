<div style="max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center;">
    <h2 style="margin-bottom: 20px;"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>
    
    <?php if (!empty($erro)): ?>
        <p style="color: red; font-size: 0.9em; margin-bottom: 15px;"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form action="/financas/auth/login" method="POST" class="d-flex flex-column">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
        
        <input type="email" name="email" placeholder="Seu E-mail" required autocomplete="email" style="margin-bottom: 15px; padding: 10px;">
        <input type="password" name="senha" placeholder="Sua Senha" required autocomplete="current-password" style="margin-bottom: 20px; padding: 10px;">
        
        <button type="submit" style="padding: 12px; font-size: 16px;">Entrar</button>
    </form>

    <div style="margin-top: 20px; font-size: 0.9em;">
        Não tem uma conta? <a href="/financas/auth/registro" style="color: #007BFF; text-decoration: none; font-weight: bold;">Crie aqui</a>
    </div>
</div>