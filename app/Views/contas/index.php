<div class="transactions-container">
    
    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-bank" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/contas/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Banco</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <input type="text" name="nome_banco" class="form-control" placeholder="Ex: Nubank" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Saldo Inicial (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="text" name="saldo_inicial" class="form-control" placeholder="Ex: 1.500,00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Cor de Identificação</label>
                    <div class="input-with-icon">
                        <i class="ph ph-palette"></i>
                        <input type="color" name="cor_identificacao" class="form-control color-picker" value="#000000" title="Escolha a cor de identificação">
                    </div>
                </div>
            </div>
            
            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-plus-circle"></i> Salvar Conta
                </button>
            </div>
        </form>
    </div>

    <div class="card table-container">
        <div class="card-header">
            <h4>Contas Cadastradas</h4>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Banco</th>
                        <th>Cor</th>
                        <th>Saldo Inicial</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($contas) > 0): ?>
                        <?php foreach ($contas as $item): ?>
                            <tr>
                                <td class="font-medium"><?= htmlspecialchars($item['nome_banco'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <div class="color-indicator" style="background-color: <?= htmlspecialchars($item['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>;" title="Cor: <?= htmlspecialchars($item['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>"></div>
                                </td>
                                <td class="font-medium">R$ <?= number_format($item['saldo_inicial'], 2, ',', '.') ?></td>
                                <td class="text-right">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="/financas/contas/edit/<?= $item['id_conta'] ?>" class="icon-btn-sm" title="Editar">
                                            <i class="ph ph-pencil-simple"></i>
                                        </a>
                                        
                                        <form action="/financas/contas/delete/<?= $item['id_conta'] ?>" method="POST" style="margin: 0;">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="icon-btn-sm danger" title="Remover" onclick="return confirm('Tem certeza que deseja remover esta conta?');">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 32px; color: var(--text-secondary);">
                                <i class="ph ph-bank" style="font-size: 32px; opacity: 0.5; margin-bottom: 8px; display: block;"></i>
                                Nenhuma conta cadastrada.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>