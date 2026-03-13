<div style="max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center;">
    <h2 style="margin-bottom: 20px;"><?= $titulo ?></h2>
    
    <?php if (!empty($erro)): ?>
        <p style="color: red; font-size: 0.9em; margin-bottom: 15px;"><?= $erro ?></p>
    <?php endif; ?>

    <?php if (!empty($sucesso)): ?>
        <p style="color: green; font-size: 0.9em; margin-bottom: 15px;"><?= $sucesso ?></p>
    <?php endif; ?>

    <form action="/financas/auth/registro" method="POST" class="d-flex flex-column">
        <input type="text" name="nome" placeholder="Nome Completo" required style="margin-bottom: 15px; padding: 10px;">
        <input type="email" name="email" placeholder="Seu E-mail" required style="margin-bottom: 15px; padding: 10px;">
        <input type="password" name="senha" placeholder="Crie uma Senha" required style="margin-bottom: 20px; padding: 10px;">
        <button type="submit" style="padding: 12px; font-size: 16px;">Cadastrar</button>
    </form>

    <div style="margin-top: 20px; font-size: 0.9em;">
        Já possui conta? <a href="/financas/auth/login" style="color: #007BFF; text-decoration: none; font-weight: bold;">Faça Login</a>
    </div>
</div>