<div class="transactions-container">
    <div style="display: flex; gap: 8px; margin-bottom: 20px;">
        <a href="/financas/metas" class="btn-outline" style="padding: 6px 12px; text-decoration: none;"><i class="ph ph-arrow-left"></i> Voltar</a>
    </div>

    <?php
        $objetivo = (float)$meta['valor_objetivo'];
        $atual = (float)$meta['valor_atual'];
        $porcentagem = ($objetivo > 0) ? ($atual / $objetivo) * 100 : 0;
        $larguraBarra = ($porcentagem > 100) ? 100 : $porcentagem;
        $falta = max(0, $objetivo - $atual);
    ?>

    <!-- Header da Meta -->
    <div class="card mb-4" style="background: linear-gradient(135deg, var(--color-ia-purple), #6b21a8); color: white; border: none;">
        <div class="card-body" style="padding: 32px 24px; text-align: center;">
            <h3 style="margin: 0; font-weight: 500; opacity: 0.9;"><i class="ph ph-flag-checkered" style="vertical-align: middle;"></i> <?= htmlspecialchars($meta['titulo_meta']) ?></h3>
            
            <h1 style="margin: 10px 0 5px 0; font-size: 2.5rem; font-weight: 700;">R$ <?= number_format($atual, 2, ',', '.') ?></h1>
            <p style="margin: 0; opacity: 0.85; font-size: 1rem;">de R$ <?= number_format($objetivo, 2, ',', '.') ?></p>
            
            <div style="width: 100%; max-width: 400px; margin: 20px auto 0 auto; background: rgba(255,255,255,0.2); border-radius: 10px; height: 12px; overflow: hidden;">
                <div style="width: <?= $larguraBarra ?>%; background: white; height: 100%; border-radius: 10px; transition: width 0.5s ease;"></div>
            </div>
            <p style="margin: 8px 0 0 0; font-weight: bold; font-size: 1.1rem;"><?= number_format($porcentagem, 1) ?>% concluído</p>
            
            <?php if ($falta > 0): ?>
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; opacity: 0.8;">Faltam R$ <?= number_format($falta, 2, ',', '.') ?></p>
            <?php else: ?>
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: #10b981; font-weight: bold; text-shadow: 0 1px 2px rgba(0,0,0,0.2);"><i class="ph-fill ph-check-circle"></i> Parabéns! Você atingiu esta meta!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Novo Aporte Rápido -->
    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-piggy-bank" style="margin-right: 8px;"></i> Guardar Dinheiro</h4>
        </div>
        <form action="/financas/metas/storeAporte/<?= $meta['id_meta'] ?>" method="POST" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group" style="flex: 1; min-width: 120px; margin-bottom: 0;">
                <label>Valor Guardado (R$)</label>
                <input type="number" step="0.01" name="valor" class="form-control" placeholder="0.00" required style="padding: 8px 12px;">
            </div>
            
            <div class="form-group" style="flex: 1; min-width: 140px; margin-bottom: 0;">
                <label>Data</label>
                <input type="date" name="data_movimento" class="form-control" value="<?= date('Y-m-d') ?>" required style="padding: 8px 12px;">
            </div>
            
            <div class="form-group" style="flex: 2; min-width: 180px; margin-bottom: 0;">
                <label>Observação (Opcional)</label>
                <input type="text" name="observacao" class="form-control" placeholder="Ex: Aporte do mês, dinheiro extra..." style="padding: 8px 12px;">
            </div>
            
            <button type="submit" class="btn-primary" style="height: 42px; padding: 0 20px; white-space: nowrap;"><i class="ph ph-plus-circle"></i> Depositar</button>
        </form>
    </div>

    <!-- Tabela do Extrato da Meta -->
    <div class="card table-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-list-dashes" style="margin-right: 8px;"></i> Histórico de Aportes</h4>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Operação</th>
                        <th>Descrição</th>
                        <th class="text-right">Valor Guardado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($historico)): ?>
                        <?php foreach ($historico as $h): ?>
                            <tr>
                                <td class="text-secondary"><?= date('d/m/Y', strtotime($h['data_movimento'])) ?></td>
                                <td><span class="badge"><i class="ph ph-arrow-down-left"></i> Depósito</span></td>
                                <td><?= htmlspecialchars($h['observacao'] ?? '-') ?></td>
                                <td class="text-right positive font-medium">
                                    + R$ <?= number_format($h['valor'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-secondary" style="padding: 24px;">Nenhum aporte registrado ainda. Comece a guardar!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Editar Configurações Básicas (Fica pequeno no final, caso precise mudar título ou prazo) -->
    <div class="card form-container" style="border: 1px dashed var(--border-color); background: transparent; box-shadow: none;">
        <div class="card-header" style="cursor: pointer;" onclick="document.getElementById('config-meta').style.display = document.getElementById('config-meta').style.display === 'none' ? 'block' : 'none';">
            <h4><i class="ph ph-gear" style="margin-right: 8px;"></i> Configurações da Meta (Clique para expandir)</h4>
        </div>
        <div id="config-meta" style="display: none; padding-top: 15px;">
            <form action="/financas/metas/update/<?= $meta['id_meta'] ?>" method="POST" class="transaction-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                
                <!-- Truque de segurança: mandamos o valor atual invisível para não zerar -->
                <input type="hidden" name="valor_atual" value="<?= $meta['valor_atual'] ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Título da Meta</label>
                        <input type="text" name="titulo_meta" class="form-control" value="<?= htmlspecialchars($meta['titulo_meta']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Data Limite</label>
                        <input type="date" name="data_limite" class="form-control" value="<?= $meta['data_limite'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Objetivo Total (R$)</label>
                        <input type="number" step="0.01" name="valor_objetivo" class="form-control" value="<?= $meta['valor_objetivo'] ?>" required>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 15px;">
                    <button type="submit" class="btn-outline w-full text-center" style="justify-content: center;">Salvar Configurações</button>
                </div>
            </form>
        </div>
    </div>
</div>