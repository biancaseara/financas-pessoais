<h1><?= $titulo ?></h1>

<div class="card-container">
    <div class="card">
        <h3>Total Recebido</h3>
        <div class="valor-verde">R$ <?= number_format($resumo['entrada'], 2, ',', '.') ?></div>
    </div>
    <div class="card">
        <h3>Total Gasto</h3>
        <div class="valor-vermelho">R$ <?= number_format($resumo['saida'], 2, ',', '.') ?></div>
    </div>
    <div class="card">
        <h3>Balanço Geral</h3>
        <div style="font-size: 24px; font-weight: bold; color: <?= $resumo['saldo'] >= 0 ? 'blue' : 'red' ?>;">
            R$ <?= number_format($resumo['saldo'], 2, ',', '.') ?>
        </div>
    </div>
</div>

<h2>Últimas Movimentações</h2>
<table>
    <tr>
        <th>Data</th>
        <th>Descrição</th>
        <th>Categoria</th>
        <th>Banco</th>
        <th>Valor</th>
    </tr>
    <?php if (count($recentes) > 0): ?>
        <?php foreach ($recentes as $r): ?>
            <?php 
                // Sem acento na Saida
                if ($r['tipo_transacao'] == 'Entrada') {
                    $cor = 'green';
                } elseif ($r['tipo_transacao'] == 'Saida') {
                    $cor = 'red';
                } else {
                    $cor = 'blue';
                }
            ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($r['data_transacao'])) ?></td>
                <td><?= htmlspecialchars($r['descricao']) ?></td>
                <td><?= htmlspecialchars($r['nome_categoria'] ?? '🔄 Transferência') ?></td>
                <td><?= htmlspecialchars($r['nome_banco'] ?? '—') ?></td>
                <td style="color: <?= $cor ?>; font-weight: bold;">
                    R$ <?= number_format($r['valor'], 2, ',', '.') ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" style="text-align: center;">Nenhuma movimentação recente.</td>
        </tr>
    <?php endif; ?>
</table>