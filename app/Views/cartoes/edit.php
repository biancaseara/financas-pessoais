<div class="transactions-container">
    <div class="card form-container">
        <div class="card-header">
            <h4><i class="ph ph-pencil-simple" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/cartoes/update/<?= $cartao['id_cartao'] ?>" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Cartão</label>
                    <div class="input-with-icon">
                        <i class="ph ph-identification-card"></i>
                        <input type="text" name="nome_cartao" class="form-control" value="<?= htmlspecialchars($cartao['nome_cartao'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Limite Total (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="text" name="limite_total" class="form-control" value="<?= number_format($cartao['limite_total'], 2, ',', '.') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dia de Fechamento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-x"></i>
                        <input type="number" name="dia_fechamento" class="form-control" value="<?= htmlspecialchars($cartao['dia_fechamento'], ENT_QUOTES, 'UTF-8') ?>" min="1" max="31" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dia de Vencimento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-check"></i>
                        <input type="number" name="dia_vencimento" class="form-control" value="<?= htmlspecialchars($cartao['dia_vencimento'], ENT_QUOTES, 'UTF-8') ?>" min="1" max="31" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Cor de Identificação</label>
                    <div class="input-with-icon">
                        <i class="ph ph-palette"></i>
                        <input type="color" name="cor_identificacao" class="form-control color-picker" value="<?= htmlspecialchars($cartao['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>" title="Cor do Cartão">
                    </div>
                </div>
            </div>
            
            <div class="form-actions" style="margin-top: 24px; display: flex; gap: 16px;">
                <a href="/financas/cartoes" class="btn-outline flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-check-circle"></i> Atualizar Cartão
                </button>
            </div>
        </form>
    </div>
</div>