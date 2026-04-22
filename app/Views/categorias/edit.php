<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/categorias/update/<?= $categoria['id_categoria'] ?>" method="POST" class="d-flex flex-column">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <div class="d-flex" style="gap: 10px;">
        <input name="nome_categoria" value="<?= htmlspecialchars($categoria['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>" required style="flex-grow: 1;">
        
        <select name="tipo" id="tipo_categoria" required>
            <option value="R" <?= ($categoria['tipo'] == 'R') ? 'selected' : '' ?>>Receita (Entrada)</option>
            <option value="D" <?= ($categoria['tipo'] == 'D') ? 'selected' : '' ?>>Despesa (Saída)</option>
        </select>
        
        <input type="text" name="limite_mensal" id="box_limite" value="<?= !empty($categoria['limite_mensal']) ? number_format($categoria['limite_mensal'], 2, ',', '.') : '' ?>" placeholder="Limite (Ex: 1.500,00)" style="flex-grow: 1; <?= ($categoria['tipo'] == 'R') ? 'display:none;' : '' ?>">
    </div>
    
    <div class="d-flex" style="margin-top: 20px; gap: 10px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer;">Atualizar Categoria</button>
        <a href="/financas/categorias" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#tipo_categoria').change(function() {
        if ($(this).val() == 'D') {
            $('#box_limite').show();
        } else {
            $('#box_limite').hide().val('');
        }
    });
});
</script>