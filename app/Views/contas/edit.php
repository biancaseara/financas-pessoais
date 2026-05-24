<div class="transactions-container">
    <div class="card form-container">
        <div class="card-header">
            <h4><i class="ph ph-pencil-simple" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/contas/update/<?= $conta['id_conta'] ?>" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Banco</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <input type="text" name="nome_banco" class="form-control" value="<?= htmlspecialchars($conta['nome_banco'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Saldo Inicial (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="text" name="saldo_inicial" class="form-control" value="<?= number_format($conta['saldo_inicial'], 2, ',', '.') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Cor de Identificação</label>
                    <div class="input-with-icon">
                        <i class="ph ph-palette"></i>
                        <input type="color" name="cor_identificacao" class="form-control color-picker" value="<?= htmlspecialchars($conta['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-actions" style="margin-top: 24px; display: flex; gap: 16px;">
                <a href="/financas/contas" class="btn-outline flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-check-circle"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>