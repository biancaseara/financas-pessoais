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

<?php if (!empty($orcamentos)): ?>
    <h2 style="margin-top: 30px;">Limites de Gastos (Neste Mês)</h2>
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        
        <?php foreach ($orcamentos as $orc): ?>
            <?php 
                $gasto = $orc['total_gasto'];
                $limite = $orc['limite_mensal'];
                $porcentagem = ($gasto / $limite) * 100;
                $larguraBarra = ($porcentagem > 100) ? 100 : $porcentagem;
                
                // Lógica de cores inteligente
                if ($porcentagem < 60) {
                    $corBarra = '#28a745'; // Verde (Tranquilo)
                } elseif ($porcentagem < 85) {
                    $corBarra = '#ffc107'; // Amarelo (Atenção)
                } else {
                    $corBarra = '#dc3545'; // Vermelho (Perigo/Estourou)
                }
            ?>
            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px;">
                    <b><?= htmlspecialchars($orc['nome_categoria']) ?></b>
                    <span>R$ <?= number_format($gasto, 2, ',', '.') ?> / R$ <?= number_format($limite, 2, ',', '.') ?> (<?= number_format($porcentagem, 1) ?>%)</span>
                </div>
                <div style="background-color: #e9ecef; width: 100%; height: 12px; border-radius: 6px; overflow: hidden;">
                    <div style="background-color: <?= $corBarra ?>; width: <?= $larguraBarra ?>%; height: 100%; transition: width 0.5s ease;"></div>
                </div>
            </div>
        <?php endforeach; ?>
        
    </div>
<?php endif; ?>

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