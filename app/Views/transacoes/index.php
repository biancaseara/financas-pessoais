<div class="transactions-container">

    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-plus-circle" style="margin-right: 8px;"></i> <?= htmlspecialchars('Nova Transação', ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/transacoes/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group type-toggle">
                <label class="radio-card expense">
                    <input type="radio" name="tipo_transacao" value="Saida" checked>
                    <div class="radio-content">
                        <i class="ph ph-arrow-circle-down"></i>
                        <span>Saída</span>
                    </div>
                </label>
                <label class="radio-card income">
                    <input type="radio" name="tipo_transacao" value="Entrada">
                    <div class="radio-content">
                        <i class="ph ph-arrow-circle-up"></i>
                        <span>Entrada</span>
                    </div>
                </label>
                <label class="radio-card transfer">
                    <input type="radio" name="tipo_transacao" value="Transferencia">
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
                        <input type="date" name="data_transacao" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="form-group" id="linha_metodo">
                    <label>Forma de Pagamento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-wallet"></i>
                        <select name="forma_pagamento" id="forma_pagamento" class="form-control">
                            <option value="Débito">Débito</option>
                            <option value="Pix">Pix</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Dinheiro">Dinheiro Vivo</option>
                            <option value="Crédito">Cartão de Crédito</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_cartao_container" style="display: none;">
                    <label>Cartão de Crédito</label>
                    <div class="input-with-icon">
                        <i class="ph ph-credit-card"></i>
                        <select name="id_cartao" id="box_cartao" class="form-control">
                            <option value="" disabled selected>Escolha o Cartão</option>
                            <?php if (!empty($cartoes)): ?>
                                <?php foreach ($cartoes as $cartao): ?>
                                    <option value="<?= $cartao['id_cartao'] ?>">
                                        <?= htmlspecialchars($cartao['nome_cartao'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhum cartão cadastrado</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_parcelas_container" style="display: none;">
                    <label>Parcelas</label>
                    <div class="input-with-icon">
                        <i class="ph ph-list-numbers"></i>
                        <select name="parcelas" id="box_parcelas" class="form-control">
                            <?php for($i=1; $i<=24; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?>x</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_conta_container">
                    <label>Conta Origem</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <select name="id_conta" id="box_conta" class="form-control" required>
                            <option value="" disabled selected>Conta Origem</option>
                            <?php foreach ($contas as $c): ?>
                                <option value="<?= $c['id_conta'] ?>">
                                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?> (R$ <?= number_format($c['saldo_inicial'], 2, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_destino_container" style="display: none;">
                    <label>Conta Destino</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <select name="id_conta_destino" id="box_destino" class="form-control">
                            <option value="" disabled selected>Conta Destino</option>
                            <?php foreach ($contas as $c): ?>
                                <option value="<?= $c['id_conta'] ?>">
                                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_categoria_container">
                    <label>Categoria</label>
                    <div class="input-with-icon">
                        <i class="ph ph-tag"></i>
                        <select name="id_categoria" id="box_categoria" class="form-control" required>
                            <option value="" disabled selected>Escolha a Categoria</option>
                            <optgroup label="Despesas (Saídas)">
                            <?php foreach ($categorias as $cat): ?>
                                <?php if ($cat['tipo'] == 'D'): ?>
                                    <option value="<?= $cat['id_categoria'] ?>">
                                        <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Receitas (Entradas)">
                            <?php foreach ($categorias as $cat): ?>
                                <?php if ($cat['tipo'] == 'R'): ?>
                                    <option value="<?= $cat['id_categoria'] ?>">
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
                        <input type="text" name="valor" class="form-control value-input" placeholder="0,00" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Descrição</label>
                    <div class="input-with-icon">
                        <i class="ph ph-text-aa"></i>
                        <input type="text" name="descricao" class="form-control" placeholder="Ex: Mercado, Conta de Luz..." required>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary w-full"><i class="ph ph-check-circle"></i> Registrar Transação</button>
            </div>
        </form>
    </div>

    <div class="card table-container">
        <div class="card-header">
            <h4><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Origem</th>
                        <th>Forma</th>
                        <th>Categoria</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($transacoes) > 0): ?>
                        <?php foreach ($transacoes as $item): ?>
                            <?php 
                                $dataBr = date('d/m/Y', strtotime($item['data_transacao']));
                                
                                if ($item['tipo_transacao'] == 'Entrada') {
                                    $corValor = 'positive font-medium';
                                    $sinal = '+ ';
                                } elseif ($item['tipo_transacao'] == 'Saida') {
                                    $corValor = 'negative font-medium';
                                    $sinal = '- ';
                                } else {
                                    $corValor = 'font-medium style="color: var(--color-ia-purple);"';
                                    $sinal = '';
                                }

                                $origem = $item['nome_banco'] ?? 'Fatura de Cartão';
                                $iconeOrigem = $item['nome_banco'] ? 'ph-bank' : 'ph-credit-card';
                                $formaPagamento = $item['forma_pagamento'] ?? 'Outros';
                            ?>
                            <tr>
                                <td class="text-secondary"><?= $dataBr ?></td>
                                <td>
                                    <span class="account-tag"><i class="ph <?= $iconeOrigem ?>"></i> <?= htmlspecialchars($origem, ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="font-medium text-secondary">
                                    <?= htmlspecialchars($formaPagamento, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <span class="badge"><?= htmlspecialchars($item['nome_categoria'] ?? 'Transferência', ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="font-medium"><?= htmlspecialchars($item['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="<?= $corValor ?>">
                                    <?= $sinal ?>R$ <?= number_format($item['valor'], 2, ',', '.') ?>
                                </td>
                                <td class="text-right">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="/financas/transacoes/edit/<?= $item['id_transacao'] ?>" class="icon-btn-sm" title="Editar">
                                            <i class="ph ph-pencil-simple"></i>
                                        </a>
                                        
                                        <form action="/financas/transacoes/delete/<?= $item['id_transacao'] ?>" method="POST" style="margin: 0;">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="icon-btn-sm danger" title="Excluir" onclick="return confirm('Apagar transação e reverter saldos?');">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; padding: 32px; color: var(--text-secondary);">Nenhuma transação registrada.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('input[name="tipo_transacao"]').change(function() {
        let tipo = $(this).val();
        
        if (tipo == 'Transferencia') {
            $('#box_destino_container').show();
            $('#box_destino').prop('required', true);
            $('#box_categoria_container').hide();
            $('#box_categoria').prop('required', false).val('');
            $('#linha_metodo').hide(); 
            $('#forma_pagamento').val('Outros').trigger('change');
        } else if (tipo == 'Entrada') {
            $('#box_destino_container').hide();
            $('#box_destino').prop('required', false).val('');
            $('#box_categoria_container').show();
            $('#box_categoria').prop('required', true);
            $('#linha_metodo').show();
            
            if($('#forma_pagamento').val() == 'Crédito') {
                $('#forma_pagamento').val('Pix').trigger('change');
            }
        } else {
            // Se for Saída
            $('#box_destino_container').hide();
            $('#box_destino').prop('required', false).val('');
            $('#box_categoria_container').show();
            $('#box_categoria').prop('required', true);
            $('#linha_metodo').show();
        }
    });

    // Lógica para Forma de Pagamento
    $('#forma_pagamento').change(function() {
        if ($(this).val() == 'Crédito') {
            $('#box_cartao_container').show();
            $('#box_cartao').prop('required', true);
            $('#box_parcelas_container').show();
            $('#box_conta_container').hide();
            $('#box_conta').prop('required', false).val('');
        } else {
            $('#box_cartao_container').hide();
            $('#box_cartao').prop('required', false).val('');
            $('#box_parcelas_container').hide();
            $('#box_parcelas').val('1');
            $('#box_conta_container').show();
            $('#box_conta').prop('required', true);
        }
    });
});
</script>