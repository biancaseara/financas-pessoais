<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/transacoes/update/<?= $transacao['id_transacao'] ?>" method="POST" class="d-flex flex-column">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <div class="d-flex" style="gap: 10px;">
        <select name="tipo_transacao" id="tipo_transacao" required style="flex-grow: 1; padding: 10px;">
            <option value="Saida" <?= ($transacao['tipo_transacao'] == 'Saida') ? 'selected' : '' ?>>Saída (Gasto)</option>
            <option value="Entrada" <?= ($transacao['tipo_transacao'] == 'Entrada') ? 'selected' : '' ?>>Entrada (Ganho)</option>
            <option value="Transferencia" <?= ($transacao['tipo_transacao'] == 'Transferencia') ? 'selected' : '' ?>>Transferência</option>
        </select>
        
        <input type="date" name="data_transacao" value="<?= $transacao['data_transacao'] ?>" required style="flex-grow: 1; padding: 10px;">
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <select name="forma_pagamento" id="forma_pagamento" style="flex-grow: 1; padding: 10px;">
            <option value="Débito" <?= ($transacao['forma_pagamento'] == 'Débito') ? 'selected' : '' ?>>Débito</option>
            <option value="Pix" <?= ($transacao['forma_pagamento'] == 'Pix') ? 'selected' : '' ?>>Pix</option>
            <option value="Boleto" <?= ($transacao['forma_pagamento'] == 'Boleto') ? 'selected' : '' ?>>Boleto</option>
            <option value="Dinheiro" <?= ($transacao['forma_pagamento'] == 'Dinheiro') ? 'selected' : '' ?>>Dinheiro Vivo</option>
            <option value="Crédito" <?= ($transacao['forma_pagamento'] == 'Crédito') ? 'selected' : '' ?>>💳 Cartão de Crédito</option>
            <option value="Outros" <?= ($transacao['forma_pagamento'] == 'Outros') ? 'selected' : '' ?>>Outros</option>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <?php if ($transacao['id_fatura'] === null): ?>
            <select name="id_conta" id="box_conta" style="flex-grow: 1; padding: 10px;" required>
                <option value="" disabled <?= ($transacao['id_conta'] == null) ? 'selected' : '' ?>>Conta Origem</option>
                <?php foreach ($contas as $c): ?>
                    <option value="<?= $c['id_conta'] ?>" <?= ($transacao['id_conta'] == $c['id_conta']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <input type="text" value="💳 Bloqueado: Transação atrelada a uma Fatura" disabled style="flex-grow: 1; padding: 10px; background-color: #eee;">
        <?php endif; ?>

        <select name="id_conta_destino" id="box_destino" style="flex-grow: 1; padding: 10px; <?= ($transacao['tipo_transacao'] == 'Transferencia') ? '' : 'display: none;' ?>">
            <option value="" disabled <?= ($transacao['id_conta_destino'] == null) ? 'selected' : '' ?>>Conta Destino</option>
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>" <?= ($transacao['id_conta_destino'] == $c['id_conta']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_categoria" id="box_categoria" required style="flex-grow: 1; padding: 10px; <?= ($transacao['tipo_transacao'] == 'Transferencia') ? 'display: none;' : '' ?>">
            <option value="" disabled <?= ($transacao['id_categoria'] == null) ? 'selected' : '' ?>>Escolha a Categoria</option>
            <optgroup label="Despesas (Saídas)">
            <?php foreach ($categorias as $cat): ?>
                <?php if ($cat['tipo'] == 'D'): ?>
                    <option value="<?= $cat['id_categoria'] ?>" <?= ($transacao['id_categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                        🔴 <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
            </optgroup>
            <optgroup label="Receitas (Entradas)">
            <?php foreach ($categorias as $cat): ?>
                <?php if ($cat['tipo'] == 'R'): ?>
                    <option value="<?= $cat['id_categoria'] ?>" <?= ($transacao['id_categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                        🟢 <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
            </optgroup>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <input type="text" name="descricao" value="<?= htmlspecialchars($transacao['descricao'], ENT_QUOTES, 'UTF-8') ?>" required style="flex-grow: 1; padding: 10px;">
        <input type="text" name="valor" value="<?= number_format($transacao['valor'], 2, ',', '.') ?>" required style="padding: 10px;">
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer; background-color: #28a745; color: white; border: none; border-radius: 4px;">Atualizar Transação</button>
        <a href="/financas/transacoes" style="padding: 10px 15px; text-decoration: none; background: #6c757d; color: white; border-radius: 4px;">Cancelar</a>
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