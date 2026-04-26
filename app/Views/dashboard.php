<?php
$primeiroNome = explode(' ', $_SESSION['nome'])[0];
?>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
    <h1 style="margin: 0;">Olá, <?= htmlspecialchars($primeiroNome) ?>! 👋</h1>

    <form action="/financas/recorrentes/lancarMes" method="POST" style="margin: 0;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" style="background-color: #6f42c1; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.2);" onclick="return confirm('Deseja lançar todas as despesas fixas ativas deste mês?');">
            🤖 Lançar Despesas do Mês
        </button>
    </form>
</div>

<h2 style="margin-top: 0; margin-bottom: 10px; color: #555;">💰 Patrimônio Total Acumulado</h2>
<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 5px solid #007BFF; margin-bottom: 30px;">
    <div style="font-size: 32px; font-weight: bold; color: #333;">
        R$ <?= number_format($resumo['patrimonio'], 2, ',', '.') ?>
    </div>
    <p style="color: #666; margin: 5px 0 0 0; font-size: 14px;">Este é o dinheiro vivo somado de todas as suas contas bancárias.</p>
</div>

<h2 style="margin-top: 0; margin-bottom: 10px; color: #555;">📊 Balanço do Mês Atual (<?= date('m/Y') ?>)</h2>
<div class="card-container" style="margin-bottom: 30px;">

    <div class="card" style="border-left: 5px solid #28a745;">
        <h3 style="color: #555; font-size: 14px;">Entradas do Mês</h3>
        <div class="valor-verde" style="font-size: 24px;">R$ <?= number_format($resumo['entrada'], 2, ',', '.') ?></div>
    </div>

    <div class="card" style="border-left: 5px solid #dc3545;">
        <h3 style="color: #555; font-size: 14px;">Saídas do Mês (Débito + Crédito)</h3>
        <div class="valor-vermelho" style="font-size: 24px;">R$ <?= number_format($resumo['saida'], 2, ',', '.') ?></div>
    </div>

    <?php
    $corBalanco = ($resumo['balanco'] >= 0) ? '#28a745' : '#dc3545';
    $statusBalanco = ($resumo['balanco'] >= 0) ? 'Mês Positivo' : 'Gastou mais do que ganhou';
    ?>
    <div class="card" style="border-left: 5px solid <?= $corBalanco ?>;">
        <h3 style="color: #555; font-size: 14px;">Termômetro (Receitas - Saídas)</h3>
        <div style="font-size: 24px; font-weight: bold; color: <?= $corBalanco ?>;">
            R$ <?= number_format($resumo['balanco'], 2, ',', '.') ?>
        </div>
        <p style="margin: 5px 0 0 0; font-size: 12px; color: <?= $corBalanco ?>; font-weight: bold;"><?= $statusBalanco ?></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px;">
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; min-width: 300px; display: flex; flex-direction: column; align-items: center;">
        <h3 style="margin-bottom: 15px;">Despesas por Categoria (Mês Atual)</h3>

        <?php if (!empty($gastosPorCategoria)): ?>
            <div style="width: 100%; max-width: 350px;">
                <canvas id="graficoCategorias"></canvas>
            </div>

            <?php
            // Prepara os dados do PHP para o JavaScript ler
            $labels = [];
            $valores = [];
            foreach ($gastosPorCategoria as $gasto) {
                $labels[] = $gasto['nome_categoria'];
                $valores[] = $gasto['total'];
            }
            ?>

            <script>
                // Pega os arrays criados pelo PHP e joga pro JS
                const labelsCategoria = <?= json_encode($labels) ?>;
                const dadosCategoria = <?= json_encode($valores) ?>;

                // Configuração das cores do gráfico
                const coresGrafico = [
                    '#FF6384', '#FF8A80', '#EC407A', '#AB47BC', '#7E57C2', '#9966FF',
                    '#5C6BC0', '#42A5F5', '#36A2EB', '#26C6DA', '#00A6A6', '#4BC0C0',
                    '#A5D8DD', '#26A69A', '#66BB6A', '#8DD17E', '#9CCC65',
                    '#FFCE56', '#FFA726', '#FF9F40', '#FF7043', '#F67019', '#EA6A47',
                    '#C9CBCF'
                ];

                const ctx = document.getElementById('graficoCategorias').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labelsCategoria,
                        datasets: [{
                            data: dadosCategoria,
                            backgroundColor: labelsCategoria.map((_, i) => coresGrafico[i % coresGrafico.length]),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            </script>
        <?php else: ?>
            <p style="color: #666; margin-top: 20px;">Ainda não há despesas registradas neste mês.</p>
        <?php endif; ?>
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

            // Lógica de cores para a barra de progresso
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

<h2 style="margin-top: 30px;">Últimas Movimentações</h2>
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