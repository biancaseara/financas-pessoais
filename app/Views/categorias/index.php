<div class="transactions-container">
    
    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-tag" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/categorias/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome da Categoria</label>
                    <div class="input-with-icon">
                        <i class="ph ph-text-t"></i>
                        <input type="text" name="nome_categoria" class="form-control" placeholder="Ex: Alimentação" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Tipo de Movimentação</label>
                    <div class="input-with-icon">
                        <i class="ph ph-arrows-down-up"></i>
                        <select name="tipo" id="tipo_categoria" class="form-control" required>
                            <option value="" disabled selected>Selecione o Tipo</option>
                            <option value="R">Receita (Entrada)</option>
                            <option value="D">Despesa (Saída)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_limite_container" style="display: none;">
                    <label>Limite Mensal (Opcional)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="text" name="limite_mensal" id="box_limite" class="form-control" placeholder="Ex: 1.000,00">
                    </div>
                </div>
            </div>
            
            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-plus-circle"></i> Salvar Categoria
                </button>
            </div>
        </form>
    </div>

    <div class="card table-container">
        <div class="card-header">
            <h4>Categorias Cadastradas</h4>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Tipo</th>
                        <th>Limite Mensal</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($categorias) > 0): ?>
                        <?php foreach ($categorias as $item): ?>
                            <?php 
                                $badgeClasse = ($item['tipo'] == 'R') ? 'badge-success' : 'badge-danger';
                                $labelTipo = ($item['tipo'] == 'R') ? 'Receita' : 'Despesa';
                                $iconeTipo = ($item['tipo'] == 'R') ? 'ph-arrow-circle-up' : 'ph-arrow-circle-down';
                            ?>
                            <tr>
                                <td class="font-medium"><?= htmlspecialchars($item['nome_categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="badge <?= $badgeClasse ?>">
                                        <i class="ph <?= $iconeTipo ?>"></i> <?= $labelTipo ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($item['limite_mensal'])): ?>
                                        <span class="font-medium">R$ <?= number_format($item['limite_mensal'], 2, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="text-secondary">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="/financas/categorias/edit/<?= $item['id_categoria'] ?>" class="icon-btn-sm" title="Editar">
                                            <i class="ph ph-pencil-simple"></i>
                                        </a>
                                        
                                        <form action="/financas/categorias/delete/<?= $item['id_categoria'] ?>" method="POST" style="margin: 0;">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="icon-btn-sm danger" title="Excluir" onclick="return confirm('Tem certeza que deseja apagar esta categoria?');">
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
                                <i class="ph ph-tag" style="font-size: 32px; opacity: 0.5; margin-bottom: 8px; display: block;"></i>
                                Nenhuma categoria cadastrada.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tipo_categoria').change(function() {
        if ($(this).val() == 'D') {
            $('#box_limite_container').show();
        } else {
            $('#box_limite_container').hide();
            $('#box_limite').val('');
        }
    });

    $('#tipo_categoria').trigger('change');
});
</script>