<div class="transactions-container">
    <div class="card form-container">
        <div class="card-header">
            <h4><i class="ph ph-pencil-simple" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/metas/update/<?= $meta['id_meta'] ?>" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Título da Meta</label>
                    <div class="input-with-icon">
                        <i class="ph ph-flag-checkered"></i>
                        <input type="text" name="titulo_meta" class="form-control" value="<?= htmlspecialchars($meta['titulo_meta']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Data Limite</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-blank"></i>
                        <input type="date" name="data_limite" class="form-control" value="<?= $meta['data_limite'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Objetivo Total (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="number" step="0.01" name="valor_objetivo" class="form-control" value="<?= $meta['valor_objetivo'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Valor Atual (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-piggy-bank"></i>
                        <input type="number" step="0.01" name="valor_atual" class="form-control value-input" value="<?= $meta['valor_atual'] ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px; display: flex; gap: 16px;">
                <a href="/financas/metas" class="btn-outline flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-check-circle"></i> Atualizar Progresso
                </button>
            </div>
        </form>
    </div>
</div>