<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<form action="/financas/investimentos/update/<?= $investimento['id_investimento'] ?>" method="POST" class="d-flex flex-column">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
    
    <div class="d-flex" style="gap: 10px;">
        <div style="flex-grow: 1;">
            <label style="font-weight: bold; font-size: 13px;">Nome do Investimento:</label>
            <input type="text" name="nome_investimento" value="<?= htmlspecialchars($investimento['nome_investimento']) ?>" required style="width: 100%; margin-top: 5px; padding: 10px;">
        </div>

        <div style="flex-grow: 1;">
            <label style="font-weight: bold; font-size: 13px;">Tipo de Ativo:</label>
            <select name="tipo" required style="width: 100%; margin-top: 5px; padding: 10px;">
                <optgroup label="🟢 Mais Seguros (Renda Fixa)">
                    <option value="Tesouro Direto" <?= ($investimento['tipo'] == 'Tesouro Direto') ? 'selected' : '' ?>>Tesouro Direto (Empréstimo ao Governo)</option>
                    <option value="CDB" <?= ($investimento['tipo'] == 'CDB') ? 'selected' : '' ?>>CDB (Empréstimo para Bancos)</option>
                    <option value="LCI/LCA" <?= ($investimento['tipo'] == 'LCI/LCA') ? 'selected' : '' ?>>LCI / LCA (Isento de IR)</option>
                    <option value="Poupança" <?= ($investimento['tipo'] == 'Poupança') ? 'selected' : '' ?>>Poupança (Rendimento Baixo)</option>
                </optgroup>
                <optgroup label="🟠 Maior Risco (Renda Variável)">
                    <option value="Ações" <?= ($investimento['tipo'] == 'Ações') ? 'selected' : '' ?>>Ações (Pedaços de Empresas)</option>
                    <option value="FIIs" <?= ($investimento['tipo'] == 'FIIs') ? 'selected' : '' ?>>FIIs (Fundos Imobiliários - Aluguéis)</option>
                    <option value="Criptomoedas" <?= ($investimento['tipo'] == 'Criptomoedas') ? 'selected' : '' ?>>Criptomoedas (Bitcoin, etc)</option>
                </optgroup>
                <option value="Outros" <?= ($investimento['tipo'] == 'Outros') ? 'selected' : '' ?>>Outros</option>
            </select>
        </div>
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <div style="flex-grow: 1;">
            <label style="font-weight: bold; font-size: 13px;">Corretora / Banco:</label>
            <input type="text" name="corretora" value="<?= htmlspecialchars($investimento['corretora']) ?>" required style="width: 100%; margin-top: 5px; padding: 10px;">
        </div>
        
        <div style="flex-grow: 1;">
            <label style="font-weight: bold; font-size: 13px;">Valor Atual (R$):</label>
            <input type="number" step="0.01" name="valor_aplicado" value="<?= $investimento['valor_aplicado'] ?>" required style="width: 100%; margin-top: 5px; padding: 10px; border-color: green; background-color: #f8fff8;">
        </div>
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <div style="flex-grow: 1;">
            <label style="font-weight: bold; font-size: 13px;">Data da Aplicação:</label>
            <input type="date" name="data_aplicacao" value="<?= $investimento['data_aplicacao'] ?>" required style="width: 100%; margin-top: 5px; padding: 10px;">
        </div>
        
        <div style="flex-grow: 1;">
            <label style="font-weight: bold; font-size: 13px;">Vencimento (Opcional):</label>
            <input type="date" name="vencimento" value="<?= $investimento['vencimento'] ?>" style="width: 100%; margin-top: 5px; padding: 10px;">
        </div>
    </div>

    <div class="d-flex" style="margin-top: 25px; gap: 10px;">
        <button type="submit" style="background-color: #28a745; padding: 10px 15px; cursor: pointer; color: white; border: none; border-radius: 4px;">💾 Atualizar Saldo e Salvar</button>
        <a href="/financas/investimentos" style="padding: 10px 15px; background: #ccc; color: #333; text-decoration: none; border-radius: 4px; font-weight: bold; text-align: center;">Cancelar</a>
    </div>
</form>