<?php
$primeiroNome = explode(' ', $_SESSION['nome'])[0];
?>

<?php if (isset($perfilIncompleto) && $perfilIncompleto): ?>
    <div class="alert alert-danger" style="margin-bottom: 24px; border: 1px solid var(--color-danger); background-color: rgba(239, 68, 68, 0.1); color: var(--color-danger); padding: 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
        <i class="ph ph-warning-circle" style="font-size: 24px;"></i>
        <div>
            <strong style="display: block; margin-bottom: 4px;">Desbloqueie o poder total da IA!</strong>
            <span style="font-size: 14px;">O seu perfil comportamental está incompleto. Finalize as configurações no <a href="/financas/perfil" style="color: var(--color-danger); text-decoration: underline; font-weight: bold;">Meu Perfil</a> para receber predições financeiras precisas e eliminar este aviso.</span>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($semDespesasFixas) && $semDespesasFixas && !$perfilIncompleto): ?>
    <div class="alert alert-warning" style="margin-bottom: 24px; border: 1px solid #f59e0b; background-color: rgba(245, 158, 11, 0.1); color: #b45309; padding: 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
        <i class="ph ph-calendar-blank" style="font-size: 24px;"></i>
        <div>
            <strong style="display: block; margin-bottom: 4px;">A IA precisa prever seu futuro!</strong>
            <span style="font-size: 14px;">Você ainda não cadastrou nenhuma conta recorrente. Vá em <a href="/financas/recorrentes" style="color: #b45309; text-decoration: underline; font-weight: bold;">Despesas Fixas</a> e adicione itens como Aluguel, Internet e Luz para que o motor preditivo calcule seu mês.</span>
        </div>
    </div>
<?php endif; ?>

<section class="ai-insights-panel" style="position: relative;">
    <div class="ai-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="ph-fill ph-sparkle" style="color: var(--color-ia-purple); font-size: 24px;"></i>
            <h3 style="margin: 0;">Preditiv.ia Insights</h3>
        </div>
        
        <!-- BOTÕES -->
        <div style="display: flex; gap: 8px;">
            <a href="/financas/ia/analisar" id="btn-analisar-ia" class="btn-outline" style="padding: 6px 14px; font-size: 13px; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                <i class="ph ph-arrows-clockwise"></i> Analisar Mês
            </a>
        </div>
    </div>
    
    <p class="ai-message" style="margin: 0; line-height: 1.5;">
        <?php if (!empty($conselho_ia) && !empty($conselho_ia['mensagem'])): ?>
            "<?= $conselho_ia['mensagem'] ?>"
            <br>
            <small style="color: var(--text-secondary); font-size: 12px; display: block; margin-top: 8px;">
                Analisado em: <?= date('d/m/Y \à\s H:i', strtotime($conselho_ia['data_criacao'])) ?>
            </small>
        <?php else: ?>
            "Olá, <?= htmlspecialchars($primeiroNome) ?>! Eu sou a Inteligência Artificial do seu sistema. Clique no botão <strong>'Analisar Mês'</strong> acima para eu processar o seu perfil e suas transações recentes, e gerar seu primeiro conselho personalizado."
        <?php endif; ?>
    </p>
</section>

<div class="dashboard-grid">

    <?php 
    $patrimonio = (float)$resumo['patrimonio'];
    $classePatrimonio = $patrimonio >= 0 ? 'positive' : 'negative';
    ?>
    <div class="card hero-card mb-4" style="background: linear-gradient(135deg, var(--bg-surface) 0%, rgba(139, 92, 246, 0.05) 100%); border-left: 4px solid var(--color-ia-purple);">
        <span class="card-label">Patrimônio Total Acumulado</span>
        <h2 class="card-value <?= $classePatrimonio ?>" style="font-size: 36px;">
            R$ <?= number_format($patrimonio, 2, ',', '.') ?>
        </h2>
        <span class="card-trend"><i class="ph ph-info"></i> Dinheiro somado de todas as suas contas bancárias.</span>
    </div>

    <div class="card mb-4">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 20px;">
            <h4>Balanço do Mês (<?= date('m/Y') ?>)</h4>
        </div>
        
        <div class="balance-panel-grid">
            <div class="balance-item">
                <span class="card-label">Entradas</span>
                <h3 class="card-value positive mt-2">R$ <?= number_format($resumo['entrada'], 2, ',', '.') ?></h3>
            </div>
            
            <div class="balance-item">
                <span class="card-label">Saídas (Débito + Crédito)</span>
                <h3 class="card-value negative mt-2">R$ <?= number_format($resumo['saida'], 2, ',', '.') ?></h3>
            </div>

            <?php
            $balanco = (float)$resumo['balanco'];
            $classeBalanco = $balanco >= 0 ? 'positive' : 'negative';
            $statusBalanco = $balanco >= 0 ? 'Mês Positivo' : 'Gastou mais do que ganhou';
            ?>
            <div class="balance-item thermometer-item">
                <span class="card-label">Termômetro (Receitas - Saídas)</span>
                <h3 class="card-value <?= $classeBalanco ?> mt-2">R$ <?= number_format($balanco, 2, ',', '.') ?></h3>
                
                <?php if ($balanco < 0): ?>
                    <span class="badge badge-danger mt-2" style="display: inline-block;"><?= $statusBalanco ?></span>
                <?php else: ?>
                    <span class="badge mt-2" style="display: inline-block; background-color: rgba(16, 185, 129, 0.1); color: var(--color-emerald); border: 1px solid rgba(16, 185, 129, 0.2);">
                        <?= $statusBalanco ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="content-row">
        
        <div class="card chart-container">
            <div class="card-header">
                <h4>Despesas por Categoria</h4>
            </div>
            <div class="chart-placeholder" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                <?php if (!empty($gastosPorCategoria)): ?>
                    <div style="width: 100%; max-width: 260px;">
                        <canvas id="graficoCategorias"></canvas>
                    </div>

                    <?php
                    $labels = [];
                    $valores = [];
                    foreach ($gastosPorCategoria as $gasto) {
                        $labels[] = $gasto['nome_categoria'];
                        $valores[] = $gasto['total'];
                    }
                    ?>
                    <script>
                        const labelsCategoria = <?= json_encode($labels) ?>;
                        const dadosCategoria = <?= json_encode($valores) ?>;
                        const coresGrafico = [
                            '#8b5cf6', '#10b981', '#ef4444', '#f59e0b', '#3b82f6', '#ec4899',
                            '#6366f1', '#14b8a6', '#f43f5e', '#84cc16', '#0ea5e9', '#d946ef'
                        ];
                        const ctx = document.getElementById('graficoCategorias').getContext('2d');
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: labelsCategoria,
                                datasets: [{
                                    data: dadosCategoria,
                                    backgroundColor: labelsCategoria.map((_, i) => coresGrafico[i % coresGrafico.length]),
                                    borderWidth: 0,
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { position: 'bottom', labels: { color: '#9ca3af', font: { family: 'Inter' } } } }
                            }
                        });
                    </script>
                <?php else: ?>
                    <p style="color: var(--text-secondary); margin-top: 20px;">Ainda não há despesas registradas neste mês.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card limits-container">
            <div class="card-header">
                <h4>Limites de Gastos (Neste Mês)</h4>
            </div>
            
            <?php if (!empty($orcamentos)): ?>
                <?php foreach ($orcamentos as $orc): ?>
                    <?php
                    $gasto = $orc['total_gasto'];
                    $limite = $orc['limite_mensal'];
                    $porcentagem = ($gasto / $limite) * 100;
                    $larguraBarra = ($porcentagem > 100) ? 100 : $porcentagem;

                    if ($porcentagem < 60) {
                        $corBarra = 'var(--color-emerald)'; 
                        $classeBar = 'success';
                    } elseif ($porcentagem < 85) {
                        $corBarra = '#f59e0b';
                        $classeBar = '';
                    } else {
                        $corBarra = 'var(--color-danger)'; 
                        $classeBar = 'danger';
                    }
                    ?>
                    <div class="limit-item mt-3">
                        <div class="limit-info">
                            <span><?= htmlspecialchars($orc['nome_categoria']) ?></span>
                            <span><?= number_format($porcentagem, 1) ?>%</span>
                        </div>
                        <div class="progress-bar <?= $classeBar ?>">
                            <div style="width: <?= $larguraBarra ?>%; <?= $classeBar == '' ? "background-color: $corBarra;" : "" ?>"></div>
                        </div>
                        <small class="limit-values">R$ <?= number_format($gasto, 2, ',', '.') ?> / R$ <?= number_format($limite, 2, ',', '.') ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-secondary); text-align: center; margin-top: 20px;">Nenhum limite definido.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card table-container">
        <div class="card-header">
            <h4>Últimas Movimentações</h4>
            <a href="/financas/transacoes" class="btn-outline" style="text-decoration: none;">Ver Extrato Completo</a>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Banco</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recentes) > 0): ?>
                        <?php foreach ($recentes as $r): ?>
                            <?php
                            $classeValor = '';
                            if ($r['tipo_transacao'] == 'Entrada') {
                                $classeValor = 'positive font-medium';
                            } elseif ($r['tipo_transacao'] == 'Saida') {
                                $classeValor = 'negative font-medium';
                            } else {
                                $classeValor = 'font-medium style="color: var(--color-ia-purple);"';
                            }
                            ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($r['data_transacao'])) ?></td>
                                <td><?= htmlspecialchars($r['descricao']) ?></td>
                                <td><span class="badge"><?= htmlspecialchars($r['nome_categoria'] ?? 'Transferência') ?></span></td>
                                <td><?= htmlspecialchars($r['nome_banco'] ?? '—') ?></td>
                                <td class="<?= $classeValor ?>">
                                    R$ <?= number_format($r['valor'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px; color: var(--text-secondary);">Nenhuma movimentação recente.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAnalisar = document.getElementById('btn-analisar-ia');
        if (btnAnalisar) {
            btnAnalisar.addEventListener('click', function() {
                this.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Analisando...';
                this.style.pointerEvents = 'none';
                this.style.opacity = '0.7';
            });
        }
    });
</script>