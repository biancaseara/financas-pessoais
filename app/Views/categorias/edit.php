<h2><?= $titulo ?></h2>

<form action="/financas/categorias/update/<?= $categoria['id_categoria'] ?>" method="POST" class="d-flex flex-column">
    <div class="d-flex">
        <input name="nome_categoria" value="<?= htmlspecialchars($categoria['nome_categoria']) ?>" required style="flex-grow: 1;">
        
        <select name="tipo" required>
            <option value="R" <?= $categoria['tipo'] == 'R' ? 'selected' : '' ?>>Receita (Entrada)</option>
            <option value="D" <?= $categoria['tipo'] == 'D' ? 'selected' : '' ?>>Despesa (Saída)</option>
        </select>
    </div>
    <div class="d-flex" style="margin-top: 10px;">
        <button type="submit">Salvar Alterações</button>
        <a href="/financas/categorias" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>