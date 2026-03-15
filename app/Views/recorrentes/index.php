<h2><?= $titulo ?></h2>

<form action="/financas/recorrentes/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    
    <div class="d-flex">
        <input type="text" name="descricao" placeholder="Descrição (Ex: Spotify, Internet)" required style="flex-grow: 1;">
        
        <select name="id_conta" required style="flex-grow: 1;">
            <option value="" disabled selected>Conta de Pagamento</option>
            <?php foreach ($contas as $c): ?>
                <option value="<?= $c['id_conta'] ?>"><?= htmlspecialchars($c['nome_banco']) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="id_categoria" required style="flex-grow: 1;">
            <option value="" disabled selected>Categoria</option>
            <?php foreach ($categorias as $cat): ?>
                <?php if ($cat['tipo'] == 'D'): // Mostrar apenas despesas ?>
                    <option value="<?= $cat['id_categoria'] ?>">🔴 <?= htmlspecialchars($cat['nome_categoria']) ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="d-flex" style="margin-top: 10px;">
        <input type="number" step="0.01" name="valor" placeholder="Valor Mensal (R$)" required style="flex-grow: 1;">
        <input type="number" name="dia_vencimento" placeholder="Dia do Vencimento (1 a 31)" min="1" max="31" required style="flex-grow: 1;">
    </div>

    <div class="d-flex" style="margin-top: 15px;">
        <button type="submit">Cadastrar Despesa Fixa</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<table>
    <tr>
        <th>Dia</th>
        <th>Descrição</th>
        <th>Categoria</th>
        <th>Conta</th>
        <th>Valor</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>
    <?php if (count($recorrentes) > 0): ?>
        <?php foreach ($recorrentes as $item): ?>
            <tr style="<?= ($item['status'] == 'Inativo') ? 'opacity: 0.6;' : '' ?>">
                <td style="font-weight: bold; font-size: 16px;"><?= str_pad($item['dia_vencimento'], 2, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($item['descricao']) ?></td>
                <td><?= htmlspecialchars($item['nome_categoria']) ?></td>
                <td><?= htmlspecialchars($item['nome_banco']) ?></td>
                <td style="color: red; font-weight: bold;">R$ <?= number_format($item['valor'], 2, ',', '.') ?></td>
                <td>
                    <span style="padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; background: <?= ($item['status'] == 'Ativo') ? '#d4edda; color: #155724;' : '#f8d7da; color: #721c24;' ?>;">
                        <?= $item['status'] ?>
                    </span>
                </td>
                <td style="display: flex; gap: 5px;">
                    <a href="/financas/recorrentes/edit/<?= $item['id_recorrente'] ?>" style="padding: 5px 10px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">Editar</a>
                    <form action="/financas/recorrentes/delete/<?= $item['id_recorrente'] ?>" method="POST" style="margin: 0;">
                        <button type="submit" style="background: #DC3545; padding: 5px 10px; font-size: 12px; border: none; cursor: pointer;" onclick="return confirm('Deseja excluir esta despesa recorrente?');">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7" style="text-align: center;">Nenhuma despesa fixa cadastrada.</td></tr>
    <?php endif; ?>
</table>