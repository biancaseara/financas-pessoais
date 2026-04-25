<h2><?= htmlspecialchars('Nova Transação', ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/transacoes/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <div class="d-flex" style="gap: 10px;">
        <select name="tipo_transacao" id="tipo_transacao" required style="flex-grow: 1; padding: 10px;">
            <option value="Saida">Saída (Gasto)</option>
            <option value="Entrada">Entrada (Ganho)</option>
            <option value="Transferencia">Transferência entre Contas</option>
        </select>
        
        <input type="date" name="data_transacao" value="<?= date('Y-m-d') ?>" required style="flex-grow: 1; padding: 10px;">
    </div>

    <div class="d-flex" id="linha_metodo" style="margin-top: 15px; gap: 10px;">
        <select name="forma_pagamento" id="forma_pagamento" style="flex-grow: 1; padding: 10px;">
            <option value="Débito">Débito</option>
            <option value="Pix">Pix</option>
            <option value="Boleto">Boleto</option>
            <option value="Dinheiro">Dinheiro Vivo</option>
            <option value="Crédito">💳 Cartão de Crédito</option>
        </select>

        <select name="id_cartao" id="box_cartao" style="flex-grow: 1; padding: 10px; display: none;">
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

        <select name="parcelas" id="box_parcelas" style="width: 100px; padding: 10px; display: none;">
            <?php for($i=1; $i<=24; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?>x</option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <select name="id_conta" id="box_conta" required style="flex-grow: 1; padding: 10px;">
            <option value="" disabled selected>Conta Origem</option>
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>">
                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?> (Saldo: R$ <?= number_format($c['saldo_inicial'], 2, ',', '.') ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_conta_destino" id="box_destino" style="flex-grow: 1; padding: 10px; display: none;">
            <option value="" disabled selected>Conta Destino</option>
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>">
                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_categoria" id="box_categoria" required style="flex-grow: 1; padding: 10px;">
            <option value="" disabled selected>Escolha a Categoria</option>
            
            <optgroup label="Despesas (Saídas)">
            <?php foreach ($categorias as $cat): ?>
                <?php if ($cat['tipo'] == 'D'): ?>
                    <option value="<?= $cat['id_categoria'] ?>">
                        🔴 <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
            </optgroup>

            <optgroup label="Receitas (Entradas)">
            <?php foreach ($categorias as $cat): ?>
                <?php if ($cat['tipo'] == 'R'): ?>
                    <option value="<?= $cat['id_categoria'] ?>">
                        🟢 <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
            </optgroup>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <input type="text" name="descricao" placeholder="Descrição (Ex: Movimentação para o Inter)" required style="flex-grow: 1; padding: 10px;">
        <input type="text" name="valor" placeholder="Valor (Ex: 150,00)" required style="padding: 10px;">
    </div>

    <div class="d-flex" style="margin-top: 15px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer;">Registrar Transação</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<table style="width: 100%; border-collapse: collapse; text-align: left;">
    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
        <th style="padding: 12px 8px;">Data</th>
        <th style="padding: 12px 8px;">Origem</th>
        <th style="padding: 12px 8px;">Forma</th>
        <th style="padding: 12px 8px;">Categoria</th>
        <th style="padding: 12px 8px;">Descrição</th>
        <th style="padding: 12px 8px;">Valor</th>
        <th style="padding: 12px 8px;">Ações</th>
    </tr>
    <?php if (count($transacoes) > 0): ?>
        <?php foreach ($transacoes as $item): ?>
            <?php 
                $dataBr = date('d/m/Y', strtotime($item['data_transacao']));
                
                if ($item['tipo_transacao'] == 'Entrada') {
                    $corValor = 'green';
                } elseif ($item['tipo_transacao'] == 'Saida') {
                    $corValor = 'red';
                } else {
                    $corValor = 'blue';
                }

                $origem = $item['nome_banco'] ?? '💳 Fatura de Cartão';
                $formaPagamento = $item['forma_pagamento'] ?? 'Outros';
            ?>
            <tr style="border-bottom: 1px solid #dee2e6;">
                <td style="padding: 12px 8px;"><?= $dataBr ?></td>
                <td style="padding: 12px 8px;"><?= htmlspecialchars($origem, ENT_QUOTES, 'UTF-8') ?></td>
                
                <td style="padding: 12px 8px; font-weight: bold; color: #555;">
                    <?= htmlspecialchars($formaPagamento, ENT_QUOTES, 'UTF-8') ?>
                </td>
                
                <td style="padding: 12px 8px;"><?= htmlspecialchars($item['nome_categoria'] ?? '🔄 Transferência', ENT_QUOTES, 'UTF-8') ?></td>
                
                <td style="padding: 12px 8px;"><?= htmlspecialchars($item['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                <td style="padding: 12px 8px; color: <?= $corValor ?>; font-weight:bold;">
                    R$ <?= number_format($item['valor'], 2, ',', '.') ?>
                </td>
                <td style="padding: 12px 8px;">
                    <div style="display: flex; gap: 5px; align-items: center;">
                        <a href="/financas/transacoes/edit/<?= $item['id_transacao'] ?>" style="padding: 5px 10px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">Editar</a>
                        
                        <form action="/financas/transacoes/delete/<?= $item['id_transacao'] ?>" method="POST" style="margin: 0;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" style="background: #DC3545; color: white; padding: 5px 10px; font-size: 12px; border: none; cursor: pointer; border-radius: 4px;" onclick="return confirm('Apagar transação e reverter saldos?');">Excluir</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7" style="text-align: center; padding: 20px;">Nenhuma transação registrada.</td></tr>
    <?php endif; ?>
</table>

<script>
$(document).ready(function() {
    // Lógica para Tipo de Transação
    $('#tipo_transacao').change(function() {
        let tipo = $(this).val();
        
        if (tipo == 'Transferencia') {
            $('#box_destino').show().prop('required', true);
            $('#box_categoria').hide().prop('required', false).val('');
            $('#linha_metodo').hide(); 
            $('#forma_pagamento').val('Outros').trigger('change');
        } else if (tipo == 'Entrada') {
            $('#box_destino').hide().prop('required', false).val('');
            $('#box_categoria').show().prop('required', true);
            $('#linha_metodo').show();
            
            if($('#forma_pagamento').val() == 'Crédito') {
                $('#forma_pagamento').val('Pix').trigger('change');
            }
        } else {
            // Se for Saída
            $('#box_destino').hide().prop('required', false).val('');
            $('#box_categoria').show().prop('required', true);
            $('#linha_metodo').show();
        }
    });

    // Lógica para Forma de Pagamento (Débito, Pix vs Crédito)
    $('#forma_pagamento').change(function() {
        if ($(this).val() == 'Crédito') {
            $('#box_cartao').show().prop('required', true);
            $('#box_parcelas').show();
            $('#box_conta').hide().prop('required', false).val('');
        } else {
            $('#box_cartao').hide().prop('required', false).val('');
            $('#box_parcelas').hide().val('1');
            $('#box_conta').show().prop('required', true);
        }
    });
});
</script>