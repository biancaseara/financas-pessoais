<h2><?= $titulo ?></h2>

<form action="/financas/metas/update/<?= $meta['id_meta'] ?>" method="POST" class="d-flex flex-column">
    
    <label style="margin-top: 10px; font-weight: bold;">Título da Meta:</label>
    <input type="text" name="titulo_meta" value="<?= htmlspecialchars($meta['titulo_meta']) ?>" required>

    <label style="margin-top: 10px; font-weight: bold;">Data Limite:</label>
    <input type="date" name="data_limite" value="<?= $meta['data_limite'] ?>" required>

    <div class="d-flex" style="margin-top: 10px;">
        <div style="flex-grow: 1;">
            <label style="font-weight: bold;">Objetivo Total (R$):</label>
            <input type="number" step="0.01" name="valor_objetivo" value="<?= $meta['valor_objetivo'] ?>" required style="width: 100%; margin-top: 5px;">
        </div>
        <div style="flex-grow: 1;">
            <label style="font-weight: bold;">Valor Atual (R$):</label>
            <input type="number" step="0.01" name="valor_atual" value="<?= $meta['valor_atual'] ?>" required style="width: 100%; margin-top: 5px;">
        </div>
    </div>

    <div class="d-flex" style="margin-top: 20px;">
        <button type="submit">Atualizar Progresso</button>
        <a href="/financas/metas" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>