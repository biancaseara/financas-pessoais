<h2><?= $titulo ?></h2>

<form action="/financas/investimentos/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    
    <div class="d-flex">
        <input type="text" name="nome_investimento" placeholder="Nome (Ex: Tesouro Selic - Viagem Tailândia)" required style="flex-grow: 1;">
        
        <select name="tipo" required style="flex-grow: 1;">
            <option value="" disabled selected>Tipo de Ativo</option>
            <option value="CDB">CDB</option>
            <option value="LCI/LCA">LCI / LCA</option>
            <option value="Tesouro Direto">Tesouro Direto</option>
            <option value="Ações">Ações / FIIs</option>
            <option value="Poupança">Poupança</option>
            <option value="Outros">Outros</option>
        </select>

        <input type="text" name="corretora" placeholder="Corretora / Banco (Ex: Inter)" required style="flex-grow: 1;">
    </div>

    <div class="d-flex" style="margin-top: 10px;">
        <div style="flex-grow: 1;">
            <label style="font-size: 12px; color: #666; font-weight: bold;">Valor Aplicado (R$):</label>
            <input type="number" step="0.01" name="valor_aplicado" placeholder="0.00" required style="width: 100%; margin-top: 5px;">
        </div>
        <div style="flex-grow: 1;">
            <label style="font-size: 12px; color: #666; font-weight: bold;">Data da Aplicação:</label>
            <input type="date" name="data_aplicacao" value="<?= date('Y-m-d') ?>" required style="width: 100%; margin-top: 5px;">
        </div>
        <div style="flex-grow: 1;">
            <label style="font-size: 12px; color: #666; font-weight: bold;">Vencimento (Opcional):</label>
            <input type="date" name="vencimento" style="width: 100%; margin-top: 5px;">
        </div>
    </div>

    <div class="d-flex" style="margin-top: 15px;">
        <button type="submit">Registrar Investimento</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<div style="display: flex; flex-wrap: wrap; gap: 15px;">
    <?php if (count($investimentos) > 0): ?>
        <?php foreach ($investimentos as $item): ?>
            <div style="background: white; border: 1px solid #ccc; padding: 15px; border-radius: 6px; width: 100%; max-width: 32%; box-sizing: border-box; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <h3 style="margin: 0; font-size: 16px; color: #333;"><?= htmlspecialchars($item['nome_investimento']) ?></h3>
                    <span style="background: #e9ecef; color: #495057; font-size: 11px; padding: 3px 8px; border-radius: 10px; font-weight: bold;"><?= htmlspecialchars($item['tipo']) ?></span>
                </div>

                <p style="margin: 5px 0; font-size: 14px;"><b>Instituição:</b> <?= htmlspecialchars($item['corretora']) ?></p>
                
                <p style="margin: 5px 0; font-size: 14px;">
                    <b>Valor:</b> <span style="color: green; font-weight: bold;">R$ <?= number_format($item['valor_aplicado'], 2, ',', '.') ?></span>
                </p>

                <p style="margin: 5px 0; font-size: 13px; color: #666;">
                    <b>Início:</b> <?= date('d/m/Y', strtotime($item['data_aplicacao'])) ?> <br>
                    <b>Vence em:</b> <?= !empty($item['vencimento']) ? date('d/m/Y', strtotime($item['vencimento'])) : 'Sem vencimento' ?>
                </p>

                <div style="display: flex; gap: 5px; margin-top: 15px;">
                    <a href="/financas/investimentos/edit/<?= $item['id_investimento'] ?>" style="flex-grow: 1; text-align: center; padding: 6px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">Atualizar Saldo</a>
                    
                    <form action="/financas/investimentos/delete/<?= $item['id_investimento'] ?>" method="POST" style="margin: 0;">
                        <button type="submit" style="background: #DC3545; padding: 6px; font-size: 13px; border: none; border-radius: 4px; cursor: pointer; color: white;" onclick="return confirm('Apagar este investimento?');">🗑️</button>
                    </form>
                </div>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="width: 100%; text-align: center; color: #666;">Nenhum investimento registrado. É hora de colocar o dinheiro para trabalhar!</p>
    <?php endif; ?>
</div>