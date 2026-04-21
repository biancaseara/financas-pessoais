<h2><?= $titulo ?></h2>

<form action="/financas/contas/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <div class="d-flex" style="align-items: center;">
        <input name="nome_banco" placeholder="Nome do Banco (Ex: Nubank)" required style="flex-grow: 1;">
        <input type="number" step="0.01" name="saldo_inicial" placeholder="Saldo Inicial (R$)" required>
        <input type="color" name="cor_identificacao" value="#000000" title="Escolha a cor de identificação" style="height: 35px; cursor: pointer;">
    </div>
    <div class="d-flex" style="margin-top: 10px;">
        <button type="submit">Salvar Conta</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<?php if (count($contas) > 0): ?>
    <?php foreach ($contas as $item): ?>
        <div style="background: white; border: 1px solid #ccc; margin-bottom: 10px; padding: 15px; border-radius: 4px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <b>Banco:</b> <?= htmlspecialchars($item['nome_banco']) ?> <br>
                    <b>Saldo:</b> R$ <?= number_format($item['saldo_inicial'], 2, ',', '.') ?> <br>
                    <div style="display: flex; align-items: center; gap: 5px; margin-top: 5px;">
                        <b>Cor:</b> 
                        <div style="background-color: <?= $item['cor_identificacao'] ?>; width: 20px; height: 20px; border-radius: 3px; border: 1px solid #333;"></div>
                    </div>
                </div>
                
                <div class="d-flex">
                    <a href="/financas/contas/edit/<?= $item['id_conta'] ?>" style="padding: 8px 12px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">Editar</a>
                    
                    <form action="/financas/contas/delete/<?= $item['id_conta'] ?>" method="POST" style="margin: 0;">
                        <button type="submit" style="background: #DC3545; padding: 8px 12px; font-size: 14px; border: none; cursor: pointer;" onclick="return confirm('Tem certeza que deseja remover esta conta?');">Remover</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nenhuma conta cadastrada.</p>
<?php endif; ?>