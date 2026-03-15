<h2>⚙️ <?= $titulo ?></h2>

<?php if (isset($_GET['sucesso'])): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
        ✅ Dados atualizados com sucesso!
    </div>
<?php endif; ?>

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 600px;">
    <form action="/financas/perfil/update" method="POST" class="d-flex flex-column">
        
        <label style="font-weight: bold; margin-bottom: 5px;">Nome Completo</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required style="margin-bottom: 15px;">
        
        <label style="font-weight: bold; margin-bottom: 5px;">E-mail de Acesso</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required style="margin-bottom: 15px;">

        <hr style="border: 0; border-top: 1px solid #ccc; margin: 15px 0;">

        <label style="font-weight: bold; margin-bottom: 5px;">Trocar Senha</label>
        <p style="font-size: 0.85em; color: #666; margin-top: 0; margin-bottom: 10px;">Preencha apenas se quiser alterar a sua senha atual.</p>
        <input type="password" name="senha" placeholder="Digite a nova senha">

        <div style="margin-top: 25px;">
            <button type="submit" style="background-color: #007BFF; width: 100%;">💾 Atualizar Meus Dados</button>
        </div>
    </form>
</div>