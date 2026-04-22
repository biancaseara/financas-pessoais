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

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <select name="id_conta" required style="flex-grow: 1; padding: 10px;">
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
        <th style="padding: 12px 8px;">Conta</th>
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
            ?>
            <tr style="border-bottom: 1px solid #dee2e6;">
                <td style="padding: 12px 8px;"><?= $dataBr ?></td>
                <td style="padding: 12px 8px;"><?= htmlspecialchars($item['nome_banco'], ENT_QUOTES, 'UTF-8') ?></td>
                
                <td style="padding: 12px 8px;"><?= htmlspecialchars($item['nome_categoria'] ?? '🔄 Transferência', ENT_QUOTES, 'UTF-8') ?></td>
                
                <td style="padding: 12px 8px;"><?= htmlspecialchars($item['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                <td style="padding: 12px 8px; color: <?= $corValor ?>; font-weight:bold;">
                    R$ <?= number_format($item['valor'], 2, ',', '.') ?>
                </td>
                <td style="padding: 12px 8px; display: flex; gap: 5px;">
                    <a href="/financas/transacoes/edit/<?= $item['id_transacao'] ?>" style="padding: 5px 10px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">Editar</a>
                    
                    <form action="/financas/transacoes/delete/<?= $item['id_transacao'] ?>" method="POST" style="margin: 0;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" style="background: #DC3545; color: white; padding: 5px 10px; font-size: 12px; border: none; cursor: pointer; border-radius: 4px;" onclick="return confirm('Apagar transação e reverter saldo da conta?');">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6" style="text-align: center; padding: 20px;">Nenhuma transação registrada.</td></tr>
    <?php endif; ?>
</table>

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