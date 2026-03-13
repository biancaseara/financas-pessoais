<h2>Nova Transação</h2>

<form action="/financas/transacoes/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <div class="d-flex">
        <select name="id_conta" required style="flex-grow: 1;">
            <option value="" disabled selected>Escolha a Conta</option>
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>">
                    <?= htmlspecialchars($c['nome_banco']) ?> (Saldo: R$ <?= number_format($c['saldo_inicial'], 2, ',', '.') ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_categoria" required style="flex-grow: 1;">
            <option value="" disabled selected>Escolha a Categoria</option>
            <?php foreach ($categorias as $cat): ?>
                <?php $tipoBadge = ($cat['tipo'] == 'R') ? '🟢 [RECEITA]' : '🔴 [DESPESA]'; ?>
                <option value="<?= $cat['id_categoria'] ?>">
                    <?= $tipoBadge ?> <?= htmlspecialchars($cat['nome_categoria']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 10px;">
        <input type="text" name="descricao" placeholder="Descrição (Ex: Compras do Mês)" required style="flex-grow: 1;">
        <input type="number" step="0.01" name="valor" placeholder="Valor (R$)" required>
        <input type="date" name="data_transacao" value="<?= date('Y-m-d') ?>" required>

        <select name="tipo_transacao" required>
            <option value="Saida">Saída (Gasto)</option>
            <option value="Entrada">Entrada (Ganho)</option>
        </select>
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
                $corValor = ($item['tipo_transacao'] == 'Entrada') ? 'green' : 'red';
            ?>
            <tr>
                <td><?= $dataBr ?></td>
                <td><?= htmlspecialchars($item['nome_banco']) ?></td>
                <td><?= htmlspecialchars($item['nome_categoria']) ?></td>
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
        <tr><td colspan="6" style="text-align: center;">Nenhuma transação registrada.</td></tr>
    <?php endif; ?>
</table>