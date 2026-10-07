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
                    <label style="display: flex; justify-content: space-between; align-items: center;">
                        Tipo de Ativo
                        <button type="button" onclick="document.getElementById('modalNovoTipo').style.display='block'" style="background: none; border: none; color: var(--primary-color); cursor: pointer; font-size: 0.85rem; font-weight: 600;">+ Criar Novo</button>
                    </label>
                    <div class="input-with-icon">
                        <i class="ph ph-chart-pie-slice"></i>
                        <select name="tipo" class="form-control" required>
                            <option value="" disabled selected>Qual o tipo deste ativo?</option>
                            <?php 
                            $grupoAtual = '';
                            foreach ($tipos as $t): 
                                if ($grupoAtual != $t['categoria_grupo']) {
                                    if ($grupoAtual != '') echo '</optgroup>';
                                    echo '<optgroup label="'. htmlspecialchars($t['categoria_grupo']) .'">';
                                    $grupoAtual = $t['categoria_grupo'];
                                }
                            ?>
                                <option value="<?= htmlspecialchars($t['nome_tipo']) ?>"><?= htmlspecialchars($t['nome_tipo']) ?></option>
                            <?php endforeach; if ($grupoAtual != '') echo '</optgroup>'; ?>
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
    
    $mapaGrupos = [];
    foreach ($tipos as $t) {
        $mapaGrupos[$t['nome_tipo']] = $t['categoria_grupo'];
    }

    foreach ($investimentos as $item) {
        $grupoDesteItem = $mapaGrupos[$item['tipo']] ?? 'Outros';
        
        if ($grupoDesteItem == 'Renda Fixa') {
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

    <div id="modalNovoTipo" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 400px;">
            <span class="close" onclick="document.getElementById('modalNovoTipo').style.display='none'">&times;</span>
            <h3 style="margin-bottom: 15px;">Nova Categoria de Ativo</h3>
            <form action="/financas/investimentos/storeTipo" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="form-group">
                    <label>Nome da Categoria</label>
                    <input type="text" name="nome_tipo" class="form-control" placeholder="Ex: Caixinhas do Nubank" required style="padding: 10px;">
                </div>
                
                <div class="form-group" style="margin-top: 15px;">
                    <label>Grupo de Risco</label>
                    <select name="grupo" class="form-control" required style="padding: 10px;">
                        <option value="Renda Fixa">Renda Fixa (Baixo Risco)</option>
                        <option value="Renda Variável">Renda Variável (Alto Risco)</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary w-full" style="margin-top: 20px;">Salvar Categoria</button>
            </form>
        </div>
    </div>
</div>