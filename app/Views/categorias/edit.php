<h2><?= $titulo ?></h2>

<form action="/financas/categorias/update/<?= $categoria['id_categoria'] ?>" method="POST" class="d-flex flex-column">
    <div class="d-flex">
        <input name="nome_categoria" value="<?= htmlspecialchars($categoria['nome_categoria']) ?>" required style="flex-grow: 1;">
        
        <select name="tipo" id="tipo_categoria" required>
            <option value="R" <?= ($categoria['tipo'] == 'R') ? 'selected' : '' ?>>Receita (Entrada)</option>
            <option value="D" <?= ($categoria['tipo'] == 'D') ? 'selected' : '' ?>>Despesa (Saída)</option>
        </select>
        
        <input type="number" step="0.01" name="limite_mensal" id="box_limite" value="<?= $categoria['limite_mensal'] ?>" placeholder="Limite Mensal (R$)" style="flex-grow: 1; <?= ($categoria['tipo'] == 'R') ? 'display:none;' : '' ?>">
    </div>
    
    <div class="d-flex" style="margin-top: 20px;">
        <button type="submit">Atualizar Categoria</button>
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