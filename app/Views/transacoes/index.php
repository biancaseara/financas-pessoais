<h2>Nova Transação</h2>

<form action="/financas/transacoes/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    
    <div class="d-flex">
        <select name="tipo_transacao" id="tipo_transacao" required style="flex-grow: 1;">
            <option value="Saida">Saída (Gasto)</option>
            <option value="Entrada">Entrada (Ganho)</option>
            <option value="Transferencia">Transferência entre Contas</option>
        </select>
        
        <input type="date" name="data_transacao" value="<?= date('Y-m-d') ?>" required style="flex-grow: 1;">
    </div>

    <div class="d-flex" style="margin-top: 10px;">
        <select name="id_conta" required style="flex-grow: 1;">
            <option value="" disabled selected>Conta Origem</option>
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>">
                    <?= htmlspecialchars($c['nome_banco']) ?> (Saldo: R$ <?= number_format($c['saldo_inicial'], 2, ',', '.') ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_conta_destino" id="box_destino" style="flex-grow: 1; display: none;">
            <option value="" disabled selected>Conta Destino</option>
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>">
                    <?= htmlspecialchars($c['nome_banco']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_categoria" id="box_categoria" required style="flex-grow: 1;">
            <option value="" disabled selected>Escolha a Categoria</option>
            <?php foreach ($categorias as $cat): ?>
                <?php $tipoBadge = ($cat['tipo'] == 'R') ? '🟢' : '🔴'; ?>
                <option value="<?= $cat['id_categoria'] ?>">
                    <?= $tipoBadge ?> <?= htmlspecialchars($cat['nome_categoria']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 10px;">
        <input type="text" name="descricao" placeholder="Descrição (Ex: Movimentação para o Inter)" required style="flex-grow: 1;">
        <input type="number" step="0.01" name="valor" placeholder="Valor (R$)" required>
    </div>

    <div class="d-flex" style="margin-top: 10px;">
        <button type="submit">Registrar Transação</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#tipo_transacao').change(function() {
        if ($(this).val() == 'Transferencia') {
            // Mostra a Conta Destino e torna obrigatória
            $('#box_destino').show().prop('required', true);
            // Esconde a Categoria, remove a obrigatoriedade e limpa o valor
            $('#box_categoria').hide().prop('required', false).val('');
        } else {
            // Esconde a Conta Destino
            $('#box_destino').hide().prop('required', false).val('');
            // Mostra a Categoria e torna obrigatória de novo
            $('#box_categoria').show().prop('required', true);
        }
    });
});
</script>