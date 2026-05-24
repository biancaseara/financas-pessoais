<div class="transactions-container">
    <div class="card form-container">
        <div class="card-header">
            <h4><i class="ph ph-pencil-simple" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/categorias/update/<?= $categoria['id_categoria'] ?>" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome da Categoria</label>
                    <div class="input-with-icon">
                        <i class="ph ph-text-t"></i>
                        <input type="text" name="nome_categoria" class="form-control" value="<?= htmlspecialchars($categoria['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Tipo de Movimentação</label>
                    <div class="input-with-icon">
                        <i class="ph ph-arrows-down-up"></i>
                        <select name="tipo" id="tipo_categoria" class="form-control" required>
                            <option value="R" <?= ($categoria['tipo'] == 'R') ? 'selected' : '' ?>>Receita (Entrada)</option>
                            <option value="D" <?= ($categoria['tipo'] == 'D') ? 'selected' : '' ?>>Despesa (Saída)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" id="box_limite_container" style="<?= ($categoria['tipo'] == 'R') ? 'display:none;' : '' ?>">
                    <label>Limite Mensal (Opcional)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="text" name="limite_mensal" id="box_limite" class="form-control" value="<?= !empty($categoria['limite_mensal']) ? number_format($categoria['limite_mensal'], 2, ',', '.') : '' ?>" placeholder="Ex: 1.500,00">
                    </div>
                </div>
            </div>
            
            <div class="form-actions" style="margin-top: 24px; display: flex; gap: 16px;">
                <a href="/financas/categorias" class="btn-outline flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-check-circle"></i> Atualizar Categoria
                </button>
            </div>
        </form>
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
});
</script>