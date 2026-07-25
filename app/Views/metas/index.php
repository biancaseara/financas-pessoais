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

                    <div class="goal-actions" style="display: flex; gap: 8px; margin-top: auto; padding-top: 16px;">
                        <a href="/financas/ia/analisarMeta/<?= $item['id_meta'] ?>" onclick="this.innerHTML='<i class=\'ph ph-spinner ph-spin\'></i> Analisando...'; this.style.pointerEvents='none';" class="btn-ia-magic purple" style="flex: 1; padding: 8px; justify-content: center;">
                            <i class="ph-fill ph-sparkle"></i> Acelerar com IA
                        </a>

                        <a href="/financas/metas/edit/<?= $item['id_meta'] ?>" class="btn-outline btn-sm-action" style="flex: 1; padding: 8px; justify-content: center;">
                            <i class="ph ph-pencil-simple"></i> Atualizar
                        </a>
                        
                        <form action="/financas/metas/delete/<?= $item['id_meta'] ?>" method="POST" class="m-0 d-flex" style="flex: 0 0 auto;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="icon-btn-sm danger" style="height: 100%; border-radius: 8px; padding: 0 10px;" onclick="return confirm('Tem certeza que deseja desistir desta meta?');" title="Desistir da Meta">
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

    <?php if (isset($_SESSION['insight_temporario'])): ?>
        <?php
            $insightMeta = json_decode($_SESSION['insight_temporario'], true);
            unset($_SESSION['insight_temporario']);
            $temInsightMeta = (json_last_error() === JSON_ERROR_NONE && isset($insightMeta['titulo']));
        ?>

        <?php if ($temInsightMeta): ?>
            <div id="modalMetaIa" class="overlay" style="display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
                <div class="card" style="width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; padding: 0;">
                    
                    <div class="card-header" style="padding: 24px; border-bottom: 1px solid var(--border-color); margin: 0; position: sticky; top: 0; background: var(--bg-surface); z-index: 10;">
                        <h4 style="margin: 0; display: flex; align-items: center; gap: 8px; color: var(--text-primary);">
                            <i class="ph-fill ph-target" style="color: var(--color-ia-purple);"></i> Plano de Aceleração
                        </h4>
                        <button onclick="document.getElementById('modalMetaIa').style.display='none'" class="icon-btn-sm" style="margin: 0;"><i class="ph ph-x"></i></button>
                    </div>

                    <div style="padding: 24px;">
                        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 16px; color: var(--text-primary);">
                            <?= htmlspecialchars($insightMeta['titulo'], ENT_QUOTES, 'UTF-8') ?>
                        </h3>
                        
                        <div style="background: var(--bg-main); padding: 16px; border-radius: 8px; border-left: 4px solid var(--color-ia-purple); margin-bottom: 24px; color: var(--text-primary); line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($insightMeta['analise'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>

                        <p style="margin: 0 0 24px 0; line-height: 1.5; color: var(--text-primary); font-size: 15px;">
                            <i class="ph-fill ph-lightning" style="color: var(--color-emerald);"></i> 
                            <strong>Ação Imediata:</strong> <?= htmlspecialchars($insightMeta['acao_imediata'], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <?php if (!empty($insightMeta['aprendizado'])): ?>
                        <div style="background: rgba(16, 185, 129, 0.1); padding: 16px; border-radius: 8px; border-left: 4px solid var(--color-emerald); color: var(--text-primary); line-height: 1.6;">
                            <strong style="color: var(--color-emerald); display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <i class="ph-fill ph-graduation-cap" style="font-size: 20px;"></i> Preditiv.ia Ensina:
                            </strong>
                            <?= nl2br(htmlspecialchars($insightMeta['aprendizado'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>