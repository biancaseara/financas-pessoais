<h2><?= $titulo ?></h2>

<form action="/financas/transacoes/update/<?= $transacao['id_transacao'] ?>" method="POST" class="d-flex flex-column">
    <div class="d-flex">
        <select name="id_conta" required style="flex-grow: 1;">
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>" <?= ($c['id_conta'] == $transacao['id_conta']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome_banco']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_categoria" required style="flex-grow: 1;">
            <?php foreach ($categorias as $cat): ?>
                <?php $tipoBadge = ($cat['tipo'] == 'R') ? '🟢' : '🔴'; ?>
                <option value="<?= $cat['id_categoria'] ?>" <?= ($cat['id_categoria'] == $transacao['id_categoria']) ? 'selected' : '' ?>>
                    <?= $tipoBadge ?> <?= htmlspecialchars($cat['nome_categoria']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 10px;">
        <input type="text" name="descricao" value="<?= htmlspecialchars($transacao['descricao']) ?>" required style="flex-grow: 1;">
        <input type="number" step="0.01" name="valor" value="<?= $transacao['valor'] ?>" required>
        <input type="date" name="data_transacao" value="<?= $transacao['data_transacao'] ?>" required>

        <select name="tipo_transacao" required>
            <option value="Saida" <?= ($transacao['tipo_transacao'] == 'Saida') ? 'selected' : '' ?>>Saída</option>
            <option value="Entrada" <?= ($transacao['tipo_transacao'] == 'Entrada') ? 'selected' : '' ?>>Entrada</option>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 20px;">
        <button type="submit">Atualizar Transação & Saldo</button>
        <a href="/financas/transacoes" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>