<div class="transactions-container">
    <div style="display: flex; gap: 8px; margin-bottom: 20px;">
        <a href="/financas/investimentos" class="btn-outline" style="padding: 6px 12px; text-decoration: none;"><i class="ph ph-arrow-left"></i> Voltar</a>
    </div>

    <!-- Header do Ativo -->
    <div class="card mb-4" style="background: linear-gradient(135deg, var(--color-emerald), #059669); color: white; border: none;">
        <div class="card-body" style="padding: 32px 24px; text-align: center;">
            <h3 style="margin: 0; font-weight: 500; opacity: 0.9;"><?= htmlspecialchars($investimento['nome_investimento']) ?></h3>
            <h1 style="margin: 10px 0 0 0; font-size: 2.8rem; font-weight: 700;">R$ <?= number_format($investimento['valor_aplicado'], 2, ',', '.') ?></h1>
            <p style="margin: 8px 0 0 0; opacity: 0.85; font-size: 1.1rem;">
                <i class="ph ph-buildings" style="vertical-align: middle;"></i> <?= htmlspecialchars($investimento['corretora']) ?> &bull; <?= htmlspecialchars($investimento['tipo']) ?>
            </p>
        </div>
    </div>

    <?php
        $total_alocado = 0;
        if (!empty($envelopes)) {
            foreach ($envelopes as $env) {
                $total_alocado += $env['valor_alocado'];
            }
        }
        $saldo_livre = max(0, $investimento['valor_aplicado'] - $total_alocado);
        $porcentagem_alocada = ($investimento['valor_aplicado'] > 0) ? ($total_alocado / $investimento['valor_aplicado']) * 100 : 0;
    ?>

    <!-- Sistema de Envelopes (Caixinhas) -->
    <div class="card mb-4">
        <div class="card-header envelope-header">
            <h4><i class="ph ph-folder-notch-open"></i> Meus Envelopes</h4>
            <span class="badge envelope-badge">Saldo Livre: <strong>R$ <?= number_format($saldo_livre, 2, ',', '.') ?></strong></span>
        </div>
        
        <div class="card-body envelope-body">
            <!-- Barra de Progresso do Capital -->
            <div class="envelope-progress-container">
                <div class="envelope-progress-bar" style="width: <?= min(100, $porcentagem_alocada) ?>%;"></div>
            </div>

            <div class="envelopes-grid">
                
                <!-- Botão de Criar Novo -->
                <div class="envelope-create-btn" onclick="document.getElementById('formNovoEnvelope').style.display='block'; this.style.display='none';">
                    <i class="ph ph-plus-circle"></i>
                    <span>Criar Envelope</span>
                </div>

                <!-- Formulário Oculto para Criar -->
                <div id="formNovoEnvelope" class="envelope-form-container" style="display: none;">
                    <form action="/financas/investimentos/storeEnvelope/<?= $investimento['id_investimento'] ?>" method="POST" class="envelope-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="text" name="nome_envelope" class="form-control envelope-input" placeholder="Ex: Cabelo" required>
                        <input type="number" step="0.01" name="valor_alocado" class="form-control envelope-input" placeholder="R$ 0,00" required>
                        <div class="envelope-form-actions">
                            <button type="submit" class="btn-primary">Salvar</button>
                            <button type="button" class="btn-outline danger" onclick="document.getElementById('formNovoEnvelope').style.display='none'; document.getElementById('formNovoEnvelope').previousElementSibling.style.display='flex';"><i class="ph ph-x"></i></button>
                        </div>
                    </form>
                </div>

                <!-- Lista de Envelopes Existentes -->
                <?php if (!empty($envelopes)): ?>
                    <?php foreach ($envelopes as $env): ?>
                        <div class="envelope-card">
                            <div class="envelope-card-header">
                                <strong class="envelope-card-title"><i class="ph ph-tag"></i> <?= htmlspecialchars($env['nome_envelope']) ?></strong>
                                
                                <form action="/financas/investimentos/deleteEnvelope/<?= $investimento['id_investimento'] ?>/<?= $env['id_envelope'] ?>" method="POST" class="envelope-delete-form">
                                    <button type="submit" class="envelope-delete-btn" onclick="return confirm('Deletar envelope? O dinheiro voltará para o saldo livre.');"><i class="ph ph-trash"></i></button>
                                </form>
                            </div>
                            
                            <!-- Formulário rápido para atualizar saldo -->
                            <form action="/financas/investimentos/updateEnvelope/<?= $investimento['id_investimento'] ?>/<?= $env['id_envelope'] ?>" method="POST" class="envelope-update-form">
                                <input type="number" step="0.01" name="valor_alocado" class="form-control envelope-update-input" value="<?= $env['valor_alocado'] ?>" required>
                                <button type="submit" class="btn-outline envelope-update-btn" title="Atualizar Saldo"><i class="ph ph-check"></i></button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Nova Movimentação -->
    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-plus-circle" style="margin-right: 8px;"></i> Lançar Movimentação</h4>
        </div>
        <form action="/financas/investimentos/storeMovimentacao/<?= $investimento['id_investimento'] ?>" method="POST" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group" style="flex: 1; min-width: 140px; margin-bottom: 0;">
                <label>Operação</label>
                <select name="tipo_movimento" class="form-control" required style="padding: 8px 12px;">
                    <option value="Aporte">🟢 Aporte (+)</option>
                    <option value="Rendimento">📈 Rendimento (+)</option>
                    <option value="Resgate">🔴 Resgate (-)</option>
                </select>
            </div>
            
            <div class="form-group" style="flex: 1; min-width: 120px; margin-bottom: 0;">
                <label>Valor (R$)</label>
                <input type="number" step="0.01" name="valor" class="form-control" placeholder="0,00" required style="padding: 8px 12px;">
            </div>
            
            <div class="form-group" style="flex: 1; min-width: 140px; margin-bottom: 0;">
                <label>Data</label>
                <input type="date" name="data_movimento" class="form-control" value="<?= date('Y-m-d') ?>" required style="padding: 8px 12px;">
            </div>
            
            <div class="form-group" style="flex: 2; min-width: 180px; margin-bottom: 0;">
                <label>Descrição (Opcional)</label>
                <input type="text" name="observacao" class="form-control" placeholder="Ex: Juros do mês, Depósito extra..." style="padding: 8px 12px;">
            </div>
            
            <button type="submit" class="btn-primary" style="height: 42px; padding: 0 20px; white-space: nowrap;">Lançar</button>
        </form>
    </div>

    <!-- Tabela do Extrato -->
    <div class="card table-container">
        <div class="card-header">
            <h4><i class="ph ph-list-dashes" style="margin-right: 8px;"></i> Extrato de Movimentações</h4>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Operação</th>
                        <th>Descrição</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($historico)): ?>
                        <?php foreach ($historico as $h): ?>
                            <?php 
                                $dataBr = date('d/m/Y', strtotime($h['data_movimento']));
                                $sinal = ($h['tipo_movimento'] == 'Resgate') ? '-' : '+';
                                $cor = ($h['tipo_movimento'] == 'Resgate') ? 'negative' : 'positive';
                                $icone = '';
                                if($h['tipo_movimento'] == 'Aporte') $icone = 'ph-arrow-down-left';
                                if($h['tipo_movimento'] == 'Resgate') $icone = 'ph-arrow-up-right';
                                if($h['tipo_movimento'] == 'Rendimento') $icone = 'ph-trend-up';
                            ?>
                            <tr>
                                <td class="text-secondary"><?= $dataBr ?></td>
                                <td><span class="badge"><i class="ph <?= $icone ?>"></i> <?= htmlspecialchars($h['tipo_movimento']) ?></span></td>
                                <td><?= htmlspecialchars($h['observacao'] ?? '-') ?></td>
                                <td class="text-right <?= $cor ?> font-medium">
                                    <?= $sinal ?> R$ <?= number_format($h['valor'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-secondary" style="padding: 24px;">Nenhuma movimentação encontrada.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>