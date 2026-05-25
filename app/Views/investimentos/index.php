<div class="transactions-container">

    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-trend-up" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/investimentos/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Investimento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-wallet"></i>
                        <input type="text" name="nome_investimento" class="form-control" placeholder="Ex: Reserva - Nubank" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tipo de Ativo</label>
                    <div class="input-with-icon">
                        <i class="ph ph-chart-pie-slice"></i>
                        <select name="tipo" class="form-control" required>
                            <option value="" disabled selected>Qual o tipo deste ativo?</option>
                            <optgroup label="🟢 Mais Seguros (Renda Fixa)">
                                <option value="Tesouro Direto">Tesouro Direto (Empréstimo ao Governo)</option>
                                <option value="CDB">CDB (Empréstimo para Bancos)</option>
                                <option value="LCI/LCA">LCI / LCA (Isento de IR)</option>
                                <option value="Poupança">Poupança (Rendimento Baixo)</option>
                            </optgroup>
                            <optgroup label="🟠 Maior Risco (Renda Variável)">
                                <option value="Ações">Ações (Pedaços de Empresas)</option>
                                <option value="FIIs">FIIs (Fundos Imobiliários - Aluguéis)</option>
                                <option value="Criptomoedas">Criptomoedas (Bitcoin, etc)</option>
                            </optgroup>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Corretora / Banco</label>
                    <div class="input-with-icon">
                        <i class="ph ph-buildings"></i>
                        <input type="text" name="corretora" class="form-control" placeholder="Ex: Inter" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Valor Aplicado (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="number" step="0.01" name="valor_aplicado" class="form-control value-input" placeholder="0,00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Data da Aplicação</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-blank"></i>
                        <input type="date" name="data_aplicacao" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Vencimento (Opcional)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-check"></i>
                        <input type="date" name="vencimento" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-plus-circle"></i> Registrar Investimento
                </button>
            </div>
        </form>
    </div>

    <?php
    $rendaFixa = [];
    $rendaVariavel = [];
    foreach ($investimentos as $item) {
        if (in_array($item['tipo'], ['Tesouro Direto', 'CDB', 'LCI/LCA', 'Poupança'])) {
            $rendaFixa[] = $item;
        } else {
            $rendaVariavel[] = $item;
        }
    }
    ?>

    <h3 class="section-title mt-3" style="color: var(--color-emerald);">
        <i class="ph-fill ph-shield-check"></i> Renda Fixa (Mais Segurança)
    </h3>
    <div class="cards-grid mb-4">
        <?php if (count($rendaFixa) > 0): ?>
            <?php foreach ($rendaFixa as $item): ?>
                <div class="card investment-card" style="border-top: 4px solid var(--color-emerald);">
                    <div class="inv-header">
                        <h4 class="inv-title"><?= htmlspecialchars($item['nome_investimento']) ?></h4>
                        <span class="badge"><?= htmlspecialchars($item['tipo']) ?></span>
                    </div>
                    
                    <div class="inv-body">
                        <div class="inv-info">Instituição: <strong><?= htmlspecialchars($item['corretora']) ?></strong></div>
                        <div class="inv-info" style="margin-top: 8px;">Valor Atual:</div>
                        <div class="inv-value">R$ <?= number_format($item['valor_aplicado'], 2, ',', '.') ?></div>
                    </div>

                    <div class="inv-actions">
                        <a href="/financas/investimentos/edit/<?= $item['id_investimento'] ?>" class="btn-outline flex-1 text-center" style="justify-content: center;">
                            <i class="ph ph-arrows-clockwise"></i> Atualizar Saldo
                        </a>
                        <form action="/financas/investimentos/delete/<?= $item['id_investimento'] ?>" method="POST" class="m-0 d-flex" style="flex: 0 0 auto;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="icon-btn-sm danger" style="height: 100%; border-radius: 8px;" onclick="return confirm('Apagar este investimento?');" title="Excluir">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="ph ph-piggy-bank"></i>
                <p>Nenhum investimento de Renda Fixa registrado.</p>
            </div>
        <?php endif; ?>
    </div>

    <h3 class="section-title mt-3" style="color: #f59e0b;">
        <i class="ph-fill ph-chart-line-up"></i> Renda Variável e Outros
    </h3>
    <div class="cards-grid">
        <?php if (count($rendaVariavel) > 0): ?>
            <?php foreach ($rendaVariavel as $item): ?>
                <div class="card investment-card" style="border-top: 4px solid #f59e0b;">
                    <div class="inv-header">
                        <h4 class="inv-title"><?= htmlspecialchars($item['nome_investimento']) ?></h4>
                        <span class="badge"><?= htmlspecialchars($item['tipo']) ?></span>
                    </div>
                    
                    <div class="inv-body">
                        <div class="inv-info">Instituição: <strong><?= htmlspecialchars($item['corretora']) ?></strong></div>
                        <div class="inv-info" style="margin-top: 8px;">Valor Atual:</div>
                        <div class="inv-value">R$ <?= number_format($item['valor_aplicado'], 2, ',', '.') ?></div>
                    </div>

                    <div class="inv-actions">
                        <a href="/financas/investimentos/edit/<?= $item['id_investimento'] ?>" class="btn-outline flex-1 text-center" style="justify-content: center;">
                            <i class="ph ph-arrows-clockwise"></i> Atualizar Saldo
                        </a>
                        <form action="/financas/investimentos/delete/<?= $item['id_investimento'] ?>" method="POST" class="m-0 d-flex" style="flex: 0 0 auto;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="icon-btn-sm danger" style="height: 100%; border-radius: 8px;" onclick="return confirm('Apagar este investimento?');" title="Excluir">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="ph ph-chart-polar"></i>
                <p>Nenhum investimento de Renda Variável registrado.</p>
            </div>
        <?php endif; ?>
    </div>

</div>