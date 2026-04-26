<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/recorrentes/update/<?= $recorrente['id_recorrente'] ?>" method="POST" class="d-flex flex-column">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
    
    <div class="d-flex" style="gap: 10px;">
        <input type="text" name="descricao" value="<?= htmlspecialchars($recorrente['descricao'], ENT_QUOTES, 'UTF-8') ?>" required style="flex-grow: 1; padding: 10px;">
        
        <select name="id_conta" required style="flex-grow: 1; padding: 10px;">
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>" <?= ($c['id_conta'] == $recorrente['id_conta']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="id_categoria" required style="flex-grow: 1; padding: 10px;">
            <?php foreach ($categorias as $cat): ?>
                <?php if ($cat['tipo'] == 'D'): ?>
                    <option value="<?= $cat['id_categoria'] ?>" <?= ($cat['id_categoria'] == $recorrente['id_categoria']) ? 'selected' : '' ?>>
                        🔴 <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <input type="number" step="0.01" name="valor" value="<?= number_format($recorrente['valor'], 2, '.', '') ?>" required style="flex-grow: 1; padding: 10px;">
        
        <div style="flex-grow: 1; display: flex; align-items: center; gap: 10px;">
            <label style="font-weight: bold; font-size: 14px;">Vencimento:</label>
            <input type="number" name="dia_vencimento" value="<?= $recorrente['dia_vencimento'] ?>" min="1" max="31" required style="width: 80px; padding: 10px;">
        </div>

        <select name="status" required style="flex-grow: 1; padding: 10px;">
            <option value="Ativo" <?= ($recorrente['status'] == 'Ativo') ? 'selected' : '' ?>>🟢 Ativo</option>
            <option value="Inativo" <?= ($recorrente['status'] == 'Inativo') ? 'selected' : '' ?>>🔴 Inativo (Pausado)</option>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 25px; gap: 10px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer; background-color: #007BFF; color: white; border: none; border-radius: 4px;">Salvar Alterações</button>
        <a href="/financas/recorrentes" style="padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>