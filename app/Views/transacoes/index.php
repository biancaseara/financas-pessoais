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

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<h2><?= $titulo ?></h2>

<table>
    <tr>
        <th>Data</th>
        <th>Conta</th>
        <th>Categoria</th>
        <th>Descrição</th>
        <th>Valor</th>
        <th>Ações</th>
    </tr>
    <?php if (count($transacoes) > 0): ?>
        <?php foreach ($transacoes as $item): ?>
            <?php 
                $dataBr = date('d/m/Y', strtotime($item['data_transacao']));
                
                // Sem acento na Saida
                if ($item['tipo_transacao'] == 'Entrada') {
                    $corValor = 'green';
                } elseif ($item['tipo_transacao'] == 'Saida') {
                    $corValor = 'red';
                } else {
                    $corValor = 'blue';
                }
            ?>
            <tr>
                <td><?= $dataBr ?></td>
                <td><?= htmlspecialchars($item['nome_banco']) ?></td>
                
                <td><?= htmlspecialchars($item['nome_categoria'] ?? '🔄 Transferência') ?></td>
                
                <td><?= htmlspecialchars($item['descricao']) ?></td>
                <td style="color: <?= $corValor ?>; font-weight:bold;">
                    R$ <?= number_format($item['valor'], 2, ',', '.') ?>
                </td>
                <td style="display: flex; gap: 5px;">
                    <a href="/financas/transacoes/edit/<?= $item['id_transacao'] ?>" style="padding: 5px 10px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">Editar</a>
                    
                    <form action="/financas/transacoes/delete/<?= $item['id_transacao'] ?>" method="POST" style="margin: 0;">
                        <button type="submit" style="background: #DC3545; padding: 5px 10px; font-size: 12px; border: none; cursor: pointer;" onclick="return confirm('Apagar transação e reverter saldo?');">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6" style="text-align: center;">Nenhuma transação registada.</td></tr>
    <?php endif; ?>
</table>

<script>
$(document).ready(function() {
    // Sem acento no Transferencia
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