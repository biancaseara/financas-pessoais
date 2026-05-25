<div class="transactions-container">
    <div class="card form-container">
        <div class="card-header">
            <h4><i class="ph ph-pencil-simple" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/transacoes/update/<?= $transacao['id_transacao'] ?>" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group type-toggle">
                <label class="radio-card expense">
                    <input type="radio" name="tipo_transacao" value="Saida" <?= ($transacao['tipo_transacao'] == 'Saida') ? 'checked' : '' ?>>
                    <div class="radio-content">
                        <i class="ph ph-arrow-circle-down"></i>
                        <span>Saída</span>
                    </div>
                </label>
                <label class="radio-card income">
                    <input type="radio" name="tipo_transacao" value="Entrada" <?= ($transacao['tipo_transacao'] == 'Entrada') ? 'checked' : '' ?>>
                    <div class="radio-content">
                        <i class="ph ph-arrow-circle-up"></i>
                        <span>Entrada</span>
                    </div>
                </label>
                <label class="radio-card transfer">
                    <input type="radio" name="tipo_transacao" value="Transferencia" <?= ($transacao['tipo_transacao'] == 'Transferencia') ? 'checked' : '' ?>>
                    <div class="radio-content">
                        <i class="ph ph-arrows-left-right"></i>
                        <span>Transferência</span>
                    </div>
                </label>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Data</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-blank"></i>
                        <input type="date" name="data_transacao" class="form-control" value="<?= $transacao['data_transacao'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Forma de Pagamento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-wallet"></i>
                        <select name="forma_pagamento" id="forma_pagamento" class="form-control">
                            <option value="Débito" <?= ($transacao['forma_pagamento'] == 'Débito') ? 'selected' : '' ?>>Débito</option>
                            <option value="Pix" <?= ($transacao['forma_pagamento'] == 'Pix') ? 'selected' : '' ?>>Pix</option>
                            <option value="Boleto" <?= ($transacao['forma_pagamento'] == 'Boleto') ? 'selected' : '' ?>>Boleto</option>
                            <option value="Dinheiro" <?= ($transacao['forma_pagamento'] == 'Dinheiro') ? 'selected' : '' ?>>Dinheiro Vivo</option>
                            <option value="Crédito" <?= ($transacao['forma_pagamento'] == 'Crédito') ? 'selected' : '' ?>>Cartão de Crédito</option>
                            <option value="Outros" <?= ($transacao['forma_pagamento'] == 'Outros') ? 'selected' : '' ?>>Outros</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_conta_container">
                    <label>Conta Origem</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <?php if ($transacao['id_fatura'] === null): ?>
                            <select name="id_conta" id="box_conta" class="form-control" required>
                                <option value="" disabled <?= ($transacao['id_conta'] == null) ? 'selected' : '' ?>>Conta Origem</option>
                                <?php foreach ($contas as $c): ?>
                                    <option value="<?= $c['id_conta'] ?>" <?= ($transacao['id_conta'] == $c['id_conta']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" class="form-control disabled-input" value="Bloqueado: Transação atrelada a Fatura" disabled>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group" id="box_destino_container" style="<?= ($transacao['tipo_transacao'] == 'Transferencia') ? '' : 'display: none;' ?>">
                    <label>Conta Destino</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <select name="id_conta_destino" id="box_destino" class="form-control">
                            <option value="" disabled <?= ($transacao['id_conta_destino'] == null) ? 'selected' : '' ?>>Conta Destino</option>
                            <?php foreach ($contas as $c): ?>
                                <option value="<?= $c['id_conta'] ?>" <?= ($transacao['id_conta_destino'] == $c['id_conta']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_categoria_container" style="<?= ($transacao['tipo_transacao'] == 'Transferencia') ? 'display: none;' : '' ?>">
                    <label>Categoria</label>
                    <div class="input-with-icon">
                        <i class="ph ph-tag"></i>
                        <select name="id_categoria" id="box_categoria" class="form-control" required>
                            <option value="" disabled <?= ($transacao['id_categoria'] == null) ? 'selected' : '' ?>>Escolha a Categoria</option>
                            <optgroup label="Despesas (Saídas)">
                            <?php foreach ($categorias as $cat): ?>
                                <?php if ($cat['tipo'] == 'D'): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" <?= ($transacao['id_categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Receitas (Entradas)">
                            <?php foreach ($categorias as $cat): ?>
                                <?php if ($cat['tipo'] == 'R'): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" <?= ($transacao['id_categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="form-group value-group">
                    <label>Valor (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="text" name="valor" class="form-control value-input" value="<?= number_format($transacao['valor'], 2, ',', '.') ?>" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Descrição</label>
                    <div class="input-with-icon">
                        <i class="ph ph-text-aa"></i>
                        <input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($transacao['descricao'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px; display: flex; gap: 16px;">
                <a href="/financas/transacoes" class="btn-outline flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1 text-center" style="display: inline-flex; justify-content: center; background-color: var(--color-emerald);">
                    <i class="ph ph-floppy-disk"></i> Atualizar Transação
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('input[name="tipo_transacao"]').change(function() {
        if ($(this).val() == 'Transferencia') {
            $('#box_destino_container').show();
            $('#box_destino').prop('required', true);
            $('#box_categoria_container').hide();
            $('#box_categoria').prop('required', false).val('');
        } else {
            $('#box_destino_container').hide();
            $('#box_destino').prop('required', false).val('');
            $('#box_categoria_container').show();
            $('#box_categoria').prop('required', true);
        }
    });
});
</script>