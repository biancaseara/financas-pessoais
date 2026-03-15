<h2><?= $titulo ?></h2>

<form action="/financas/recorrentes/update/<?= $recorrente['id_recorrente'] ?>" method="POST" class="d-flex flex-column">
    
    <div class="d-flex">
        <input type="text" name="descricao" value="<?= htmlspecialchars($recorrente['descricao']) ?>" required style="flex-grow: 1;">
        
        <select name="id_conta" required style="flex-grow: 1;">
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>" <?= ($c['id_conta'] == $recorrente['id_conta']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome_banco']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_categoria" required style="flex-grow: 1;">
            <?php foreach ($categorias as $cat): ?>
                <?php if ($cat['tipo'] == 'D'): ?>
                    <option value="<?= $cat['id_categoria'] ?>" <?= ($cat['id_categoria'] == $recorrente['id_categoria']) ? 'selected' : '' ?>>
                        🔴 <?= htmlspecialchars($cat['nome_categoria']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 10px;">
        <input type="number" step="0.01" name="valor" value="<?= $recorrente['valor'] ?>" required style="flex-grow: 1;">
        
        <div style="flex-grow: 1; display: flex; align-items: center; gap: 10px;">
            <label style="font-weight: bold;">Dia do Vencimento:</label>
            <input type="number" name="dia_vencimento" value="<?= $recorrente['dia_vencimento'] ?>" min="1" max="31" required style="width: 80px;">
        </div>

        <select name="status" required style="flex-grow: 1;">
            <option value="Ativo" <?= ($recorrente['status'] == 'Ativo') ? 'selected' : '' ?>>🟢 Ativo</option>
            <option value="Inativo" <?= ($recorrente['status'] == 'Inativo') ? 'selected' : '' ?>>🔴 Inativo (Pausado)</option>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 25px;">
        <button type="submit">Salvar Alterações</button>
        <a href="/financas/recorrentes" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>