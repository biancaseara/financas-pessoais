<h2><?= $titulo ?></h2>

<form action="/financas/transacoes/update/<?= $transacao['id_transacao'] ?>" method="POST" class="d-flex flex-column">
    <div class="d-flex">
        <select name="tipo_transacao" id="tipo_transacao" required style="flex-grow: 1;">
            <option value="Saida" <?= ($transacao['tipo_transacao'] == 'Saida') ? 'selected' : '' ?>>Saída</option>
            <option value="Entrada" <?= ($transacao['tipo_transacao'] == 'Entrada') ? 'selected' : '' ?>>Entrada</option>
            <option value="Transferencia" <?= ($transacao['tipo_transacao'] == 'Transferencia') ? 'selected' : '' ?>>Transferência</option>
        </select>

        <input type="date" name="data_transacao" value="<?= $transacao['data_transacao'] ?>" required style="flex-grow: 1;">
    </div>

    <div class="d-flex" style="margin-top: 10px;">
        <select name="id_conta" required style="flex-grow: 1;">
            <option value="" disabled>Conta Origem</option>
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>" <?= ($c['id_conta'] == $transacao['id_conta']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome_banco']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_conta_destino" id="box_destino" style="flex-grow: 1; <?= ($transacao['tipo_transacao'] != 'Transferencia') ? 'display:none;' : '' ?>">
            <option value="" disabled <?= empty($transacao['id_conta_destino']) ? 'selected' : '' ?>>Conta Destino</option>
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>" <?= ($c['id_conta'] == $transacao['id_conta_destino']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome_banco']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_categoria" id="box_categoria" style="flex-grow: 1; <?= ($transacao['tipo_transacao'] == 'Transferencia') ? 'display:none;' : '' ?>" <?= ($transacao['tipo_transacao'] != 'Transferencia') ? 'required' : '' ?>>
            <option value="" disabled <?= empty($transacao['id_categoria']) ? 'selected' : '' ?>>Escolha a Categoria</option>
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
    </div>

    <div class="d-flex" style="margin-top: 20px;">
        <button type="submit">Atualizar Transação</button>
        <a href="/financas/transacoes" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#tipo_transacao').change(function() {
        if ($(this).val() == 'Transferencia') {
            $('#box_destino').show().prop('required', true);
            $('#box_categoria').hide().prop('required', false).val('');
        } else {
            $('#box_destino').hide().prop('required', false).val('');
            $('#box_categoria').show().prop('required', true);
        }
    });
});
</script>