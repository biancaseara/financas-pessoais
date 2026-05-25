<div class="transactions-container">

    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-target" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/metas/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Objetivo (Título da Meta)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-flag-checkered"></i>
                        <input type="text" name="titulo_meta" class="form-control" placeholder="Ex: Comprar Carro" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Data Limite</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-blank"></i>
                        <input type="date" name="data_limite" class="form-control" required title="Data Limite">
                    </div>
                </div>

                <div class="form-group">
                    <label>Quanto precisa juntar? (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="number" step="0.01" name="valor_objetivo" class="form-control" placeholder="Ex: 50000,00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Quanto já tem guardado? (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-piggy-bank"></i>
                        <input type="number" step="0.01" name="valor_atual" class="form-control" placeholder="Ex: 5000,00" required>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-plus-circle"></i> Criar Meta
                </button>
            </div>
        </form>
    </div>

    <div class="card-header mt-3" style="margin-bottom: 16px;">
        <h4 style="font-size: 18px;">Minhas Metas</h4>
    </div>

    <div class="cards-grid">
        <?php if (count($metas) > 0): ?>
            <?php foreach ($metas as $item): ?>
                <?php
                    $objetivo = $item['valor_objetivo'];
                    $atual = $item['valor_atual'];
                    $porcentagem = ($objetivo > 0) ? ($atual / $objetivo) * 100 : 0;
                    $larguraBarra = ($porcentagem > 100) ? 100 : $porcentagem;
                    $dataBr = date('d/m/Y', strtotime($item['data_limite']));
                    
                    $classeBarra = ($porcentagem >= 100) ? 'success' : 'primary';
                ?>
                <div class="card goal-card">
                    <div class="goal-header">
                        <h4 class="goal-title">
                            <i class="ph-fill ph-target" style="color: var(--color-ia-purple);"></i> 
                            <?= htmlspecialchars($item['titulo_meta']) ?>
                        </h4>
                        <span class="badge" style="background-color: var(--bg-main);"><i class="ph ph-calendar"></i> <?= $dataBr ?></span>
                    </div>

                    <div class="goal-body">
                        <div class="goal-amounts">
                            <span class="current-amount">R$ <?= number_format($atual, 2, ',', '.') ?></span>
                            <span class="target-amount">de R$ <?= number_format($objetivo, 2, ',', '.') ?></span>
                        </div>
                        
                        <div class="goal-progress-container">
                            <div class="progress-bar <?= $classeBarra ?>">
                                <div style="width: <?= $larguraBarra ?>%;"></div>
                            </div>
                            <span class="progress-text"><?= number_format($porcentagem, 1) ?>%</span>
                        </div>
                    </div>

                    <div class="goal-actions" style="display: flex; gap: 12px; margin-top: auto; padding-top: 16px;">
                        <a href="/financas/metas/edit/<?= $item['id_meta'] ?>" class="btn-outline flex-1 text-center" style="justify-content: center;">
                            <i class="ph ph-pencil-simple"></i> Atualizar
                        </a>
                        
                        <form action="/financas/metas/delete/<?= $item['id_meta'] ?>" method="POST" class="m-0 d-flex" style="flex: 0 0 auto;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="icon-btn-sm danger" style="height: 100%; border-radius: 8px;" onclick="return confirm('Tem certeza que deseja desistir desta meta?');" title="Desistir da Meta">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="ph ph-flag-checkered"></i>
                <p>Nenhuma meta definida ainda.</p>
            </div>
        <?php endif; ?>
    </div>

</div>