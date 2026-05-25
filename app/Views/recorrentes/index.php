<div class="transactions-container">

    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-calendar-plus" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/recorrentes/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Descrição</label>
                    <div class="input-with-icon">
                        <i class="ph ph-text-aa"></i>
                        <input type="text" name="descricao" class="form-control" placeholder="Ex: Spotify, Internet" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Conta de Pagamento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <select name="id_conta" class="form-control" required>
                            <option value="" disabled selected>Selecione a Conta</option>
                            <?php foreach ($contas as $c): ?>
                                <option value="<?= $c['id_conta'] ?>"><?= htmlspecialchars($c['nome_banco']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Categoria</label>
                    <div class="input-with-icon">
                        <i class="ph ph-tag"></i>
                        <select name="id_categoria" class="form-control" required>
                            <option value="" disabled selected>Selecione a Categoria</option>
                            <?php foreach ($categorias as $cat): ?>
                                <?php if ($cat['tipo'] == 'D'): // Mostrar apenas despesas ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nome_categoria']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Valor Mensal (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="number" step="0.01" name="valor" class="form-control value-input" placeholder="Ex: 50,00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dia do Vencimento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-blank"></i>
                        <input type="number" name="dia_vencimento" class="form-control" placeholder="1 a 31" min="1" max="31" required>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-plus-circle"></i> Cadastrar Despesa Fixa
                </button>
            </div>
        </form>
    </div>

    <div class="card table-container">
        <div class="card-header">
            <h4>Despesas Fixas Cadastradas</h4>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Dia</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Conta</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recorrentes) > 0): ?>
                        <?php foreach ($recorrentes as $item): ?>
                            <?php 
                                $inativo = ($item['status'] == 'Inativo');
                                $classeLinha = $inativo ? 'inactive-row' : '';
                                $badgeStatus = $inativo ? 'badge-danger' : 'badge-success';
                            ?>
                            <tr class="<?= $classeLinha ?>">
                                <td class="font-medium"><?= str_pad($item['dia_vencimento'], 2, '0', STR_PAD_LEFT) ?></td>
                                <td class="font-medium"><?= htmlspecialchars($item['descricao']) ?></td>
                                <td><span class="badge"><?= htmlspecialchars($item['nome_categoria']) ?></span></td>
                                <td><span class="account-tag"><i class="ph ph-bank"></i> <?= htmlspecialchars($item['nome_banco']) ?></span></td>
                                <td class="negative font-medium">R$ <?= number_format($item['valor'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= $badgeStatus ?>"><?= $item['status'] ?></span>
                                </td>
                                <td class="text-right">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="/financas/recorrentes/edit/<?= $item['id_recorrente'] ?>" class="icon-btn-sm" title="Editar">
                                            <i class="ph ph-pencil-simple"></i>
                                        </a>
                                        <form action="/financas/recorrentes/delete/<?= $item['id_recorrente'] ?>" method="POST" style="margin: 0;">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="icon-btn-sm danger" title="Excluir" onclick="return confirm('Deseja excluir esta despesa recorrente?');">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 32px; color: var(--text-secondary);">
                                <i class="ph ph-calendar-check" style="font-size: 32px; opacity: 0.5; margin-bottom: 8px; display: block;"></i>
                                Nenhuma despesa fixa cadastrada.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>