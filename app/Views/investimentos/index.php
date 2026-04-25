<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/investimentos/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
    
    <div class="d-flex" style="gap: 10px;">
        <input type="text" name="nome_investimento" placeholder="Nome (Ex: Reserva de Emergência - Nubank)" required style="flex-grow: 1; padding: 10px;">
        
        <select name="tipo" required style="flex-grow: 1; padding: 10px;">
            <option value="" disabled selected>Qual o tipo deste investimento?</option>
            <optgroup label="🟢 Mais Seguros (Renda Fixa)">
                <option value="Tesouro Direto">Tesouro Direto (Empréstimo ao Governo)</option>
                <option value="CDB">CDB (Empréstimo para Bancos)</option>
                <option value="LCI/LCA">LCI / LCA (Isento de Imposto de Renda)</option>
                <option value="Poupança">Poupança (Rendimento Baixo)</option>
            </optgroup>
            <optgroup label="🟠 Maior Risco (Renda Variável)">
                <option value="Ações">Ações (Pedaços de Empresas)</option>
                <option value="FIIs">FIIs (Fundos Imobiliários - Aluguéis)</option>
                <option value="Criptomoedas">Criptomoedas (Bitcoin, etc)</option>
            </optgroup>
            <option value="Outros">Outros</option>
        </select>

        <input type="text" name="corretora" placeholder="Corretora / Banco (Ex: Inter)" required style="flex-grow: 1; padding: 10px;">
    </div>

    <div class="d-flex" style="margin-top: 10px; gap: 10px;">
        <div style="flex-grow: 1;">
            <label style="font-size: 12px; color: #666; font-weight: bold;">Valor Aplicado (R$):</label>
            <input type="number" step="0.01" name="valor_aplicado" placeholder="0.00" required style="width: 100%; margin-top: 5px; padding: 10px;">
        </div>
        <div style="flex-grow: 1;">
            <label style="font-size: 12px; color: #666; font-weight: bold;">Data da Aplicação:</label>
            <input type="date" name="data_aplicacao" value="<?= date('Y-m-d') ?>" required style="width: 100%; margin-top: 5px; padding: 10px;">
        </div>
        <div style="flex-grow: 1;">
            <label style="font-size: 12px; color: #666; font-weight: bold;">Vencimento (Opcional):</label>
            <input type="date" name="vencimento" style="width: 100%; margin-top: 5px; padding: 10px;">
        </div>
    </div>

    <div class="d-flex" style="margin-top: 15px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer;">Registrar Investimento</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<?php
// Separar investimentos por risco
$rendaFixa = [];
$rendaVariavel = [];
foreach ($investimentos as $item) {
    if (in_array($item['tipo'], ['Tesouro Direto', 'CDB', 'LCI/LCA', 'Poupança'])) {
        $rendaFixa[] = $item;
    } else {
        $rendaVariavel[] = $item;
    }
}
?>

<h3 style="color: #28a745; margin-bottom: 10px;">🟢 Renda Fixa (Mais Segurança)</h3>
<div style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 30px;">
    <?php if (count($rendaFixa) > 0): ?>
        <?php foreach ($rendaFixa as $item): ?>
            <div style="background: white; border: 1px solid #ccc; padding: 15px; border-radius: 6px; width: 100%; max-width: 32%; box-sizing: border-box; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-top: 4px solid #28a745;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <h4 style="margin: 0; font-size: 16px; color: #333;"><?= htmlspecialchars($item['nome_investimento']) ?></h4>
                    <span style="background: #e9ecef; color: #495057; font-size: 11px; padding: 3px 8px; border-radius: 10px; font-weight: bold;"><?= htmlspecialchars($item['tipo']) ?></span>
                </div>
                <p style="margin: 5px 0; font-size: 14px;"><b>Instituição:</b> <?= htmlspecialchars($item['corretora']) ?></p>
                <p style="margin: 5px 0; font-size: 14px;"><b>Valor Atual:</b> <span style="color: green; font-weight: bold;">R$ <?= number_format($item['valor_aplicado'], 2, ',', '.') ?></span></p>
                <div style="display: flex; gap: 5px; margin-top: 15px;">
                    <a href="/financas/investimentos/edit/<?= $item['id_investimento'] ?>" style="flex-grow: 1; text-align: center; padding: 6px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">Atualizar Saldo</a>
                    <form action="/financas/investimentos/delete/<?= $item['id_investimento'] ?>" method="POST" style="margin: 0;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" style="background: #DC3545; padding: 6px; font-size: 13px; border: none; border-radius: 4px; cursor: pointer; color: white;" onclick="return confirm('Apagar este investimento?');">🗑️</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="width: 100%; color: #666;">Nenhum investimento de Renda Fixa registrado.</p>
    <?php endif; ?>
</div>

<h3 style="color: #fd7e14; margin-bottom: 10px;">🟠 Renda Variável e Outros (Maior Risco/Retorno)</h3>
<div style="display: flex; flex-wrap: wrap; gap: 15px;">
    <?php if (count($rendaVariavel) > 0): ?>
        <?php foreach ($rendaVariavel as $item): ?>
            <div style="background: white; border: 1px solid #ccc; padding: 15px; border-radius: 6px; width: 100%; max-width: 32%; box-sizing: border-box; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-top: 4px solid #fd7e14;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <h4 style="margin: 0; font-size: 16px; color: #333;"><?= htmlspecialchars($item['nome_investimento']) ?></h4>
                    <span style="background: #e9ecef; color: #495057; font-size: 11px; padding: 3px 8px; border-radius: 10px; font-weight: bold;"><?= htmlspecialchars($item['tipo']) ?></span>
                </div>
                <p style="margin: 5px 0; font-size: 14px;"><b>Instituição:</b> <?= htmlspecialchars($item['corretora']) ?></p>
                <p style="margin: 5px 0; font-size: 14px;"><b>Valor Atual:</b> <span style="color: green; font-weight: bold;">R$ <?= number_format($item['valor_aplicado'], 2, ',', '.') ?></span></p>
                <div style="display: flex; gap: 5px; margin-top: 15px;">
                    <a href="/financas/investimentos/edit/<?= $item['id_investimento'] ?>" style="flex-grow: 1; text-align: center; padding: 6px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">Atualizar Saldo</a>
                    <form action="/financas/investimentos/delete/<?= $item['id_investimento'] ?>" method="POST" style="margin: 0;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" style="background: #DC3545; padding: 6px; font-size: 13px; border: none; border-radius: 4px; cursor: pointer; color: white;" onclick="return confirm('Apagar este investimento?');">🗑️</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="width: 100%; color: #666;">Nenhum investimento de Renda Variável registrado.</p>
    <?php endif; ?>
</div>