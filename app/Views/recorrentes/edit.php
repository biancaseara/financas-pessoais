<div class="transactions-container">
    <div class="card form-container">
        <div class="card-header">
            <h4><i class="ph ph-pencil-simple" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/recorrentes/update/<?= $recorrente['id_recorrente'] ?>" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Descrição</label>
                    <div class="input-with-icon">
                        <i class="ph ph-text-aa"></i>
                        <input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($recorrente['descricao'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Conta de Pagamento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <select name="id_conta" class="form-control" required>
                            <?php foreach ($contas as $c): ?>
                                <option value="<?= $c['id_conta'] ?>" <?= ($c['id_conta'] == $recorrente['id_conta']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Categoria</label>
                    <div class="input-with-icon">
                        <i class="ph ph-tag"></i>
                        <select name="id_categoria" class="form-control" required>
                            <?php foreach ($categorias as $cat): ?>
                                <?php if ($cat['tipo'] == 'D'): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" <?= ($cat['id_categoria'] == $recorrente['id_categoria']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Valor Mensal (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="number" step="0.01" name="valor" class="form-control value-input" value="<?= number_format($recorrente['valor'], 2, '.', '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dia de Vencimento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-blank"></i>
                        <input type="number" name="dia_vencimento" class="form-control" value="<?= $recorrente['dia_vencimento'] ?>" min="1" max="31" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <div class="input-with-icon">
                        <i class="ph ph-toggle-left"></i>
                        <select name="status" class="form-control" required>
                            <option value="Ativo" <?= ($recorrente['status'] == 'Ativo') ? 'selected' : '' ?>>Ativo</option>
                            <option value="Inativo" <?= ($recorrente['status'] == 'Inativo') ? 'selected' : '' ?>>Inativo (Pausado)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px; display: flex; gap: 16px;">
                <a href="/financas/recorrentes" class="btn-outline flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-floppy-disk"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>