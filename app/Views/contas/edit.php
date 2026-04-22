<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/contas/update/<?= $conta['id_conta'] ?>" method="POST" class="d-flex flex-column">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <div class="d-flex" style="align-items: center; gap: 10px;">
        <input name="nome_banco" value="<?= htmlspecialchars($conta['nome_banco'], ENT_QUOTES, 'UTF-8') ?>" required style="flex-grow: 1;">
        <input type="text" name="saldo_inicial" value="<?= number_format($conta['saldo_inicial'], 2, ',', '.') ?>" required>
        <input type="color" name="cor_identificacao" value="<?= htmlspecialchars($conta['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>" style="height: 35px; cursor: pointer;">
    </div>
    
    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer;">Salvar Alterações</button>
        <a href="/financas/contas" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>