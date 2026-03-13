<h2><?= $titulo ?></h2>

<form action="/financas/categorias/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <div class="d-flex">
        <input name="nome_categoria" placeholder="Nome da Categoria (Ex: Alimentação)" required style="flex-grow: 1;">
        <select name="tipo" required>
            <option value="" disabled selected>Selecione o Tipo</option>
            <option value="R">Receita (Entrada)</option>
            <option value="D">Despesa (Saída)</option>
        </select>
    </div>
    <div class="d-flex" style="margin-top: 10px;">
        <button type="submit">Salvar Categoria</button>
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
                    <b>Categoria:</b> <?= htmlspecialchars($item['nome_categoria']) ?> <br>
                    <b>Tipo:</b> <span style="color: <?= $cor ?>; font-weight:bold;"><?= $labelTipo ?></span>
                </div>
                
                <div class="d-flex">
                    <a href="/financas/categorias/edit/<?= $item['id_categoria'] ?>" style="padding: 8px 12px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">Editar</a>
                    
                    <form action="/financas/categorias/delete/<?= $item['id_categoria'] ?>" method="POST" style="margin: 0;">
                        <button type="submit" style="background: #DC3545; padding: 8px 12px; font-size: 14px; border: none; cursor: pointer;" onclick="return confirm('Tem certeza que deseja apagar esta categoria?');">Apagar</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nenhuma categoria cadastrada.</p>
<?php endif; ?>