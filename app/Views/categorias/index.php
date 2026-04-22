<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/categorias/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <div class="d-flex" style="gap: 10px;">
        <input name="nome_categoria" placeholder="Nome da Categoria (Ex: Alimentação)" required style="flex-grow: 1;">
        
        <select name="tipo" id="tipo_categoria" required>
            <option value="" disabled selected>Selecione o Tipo</option>
            <option value="R">Receita (Entrada)</option>
            <option value="D">Despesa (Saída)</option>
        </select>

        <input type="text" name="limite_mensal" id="box_limite" placeholder="Limite Mensal (Ex: 1.000,00) - Opcional" style="display: none; flex-grow: 1;">
    </div>
    <div class="d-flex" style="margin-top: 15px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer;">Salvar Categoria</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<?php if (count($categorias) > 0): ?>
    <?php foreach ($categorias as $item): ?>
        <?php 
            $cor = ($item['tipo'] == 'R') ? 'green' : 'red';
            $labelTipo = ($item['tipo'] == 'R') ? 'RECEITA' : 'DESPESA';
        ?>
        <div style="background: white; border: 1px solid #ccc; margin-bottom: 10px; padding: 15px; border-left: 5px solid <?= $cor ?>; border-radius: 4px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <b>Categoria:</b> <?= htmlspecialchars($item['nome_categoria'], ENT_QUOTES, 'UTF-8') ?> <br>
                    <b>Tipo:</b> <span style="color: <?= $cor ?>; font-weight:bold;"><?= $labelTipo ?></span>
                    
                    <?php if (!empty($item['limite_mensal'])): ?>
                        <br><b style="color: #666;">Limite Mensal:</b> R$ <?= number_format($item['limite_mensal'], 2, ',', '.') ?>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex" style="gap: 10px; align-items: center;">
                    <a href="/financas/categorias/edit/<?= $item['id_categoria'] ?>" style="padding: 8px 12px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">Editar</a>
                    
                    <form action="/financas/categorias/delete/<?= $item['id_categoria'] ?>" method="POST" style="margin: 0;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" style="background: #DC3545; padding: 8px 12px; font-size: 14px; border: none; cursor: pointer; color: white; border-radius: 4px;" onclick="return confirm('Tem certeza que deseja apagar esta categoria?');">Apagar</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nenhuma categoria cadastrada.</p>
<?php endif; ?>

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