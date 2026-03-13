<h2><?= $titulo ?></h2>

<form action="/financas/contas/update/<?= $conta['id_conta'] ?>" method="POST" class="d-flex flex-column">
    <div class="d-flex" style="align-items: center;">
        <input name="nome_banco" value="<?= htmlspecialchars($conta['nome_banco']) ?>" required style="flex-grow: 1;">
        <input type="number" step="0.01" name="saldo_inicial" value="<?= $conta['saldo_inicial'] ?>" required>
        <input type="color" name="cor_identificacao" value="<?= $conta['cor_identificacao'] ?>" style="height: 35px; cursor: pointer;">
    </div>
    <div class="d-flex" style="margin-top: 10px;">
        <button type="submit">Salvar Alterações</button>
        <a href="/financas/contas" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>