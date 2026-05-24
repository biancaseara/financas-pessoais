<div class="transactions-container">
    <div class="card form-container">
        <div class="card-header">
            <h4><i class="ph ph-pencil-simple" style="margin-right: 8px;"></i> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/investimentos/update/<?= $investimento['id_investimento'] ?>" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Investimento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-wallet"></i>
                        <input type="text" name="nome_investimento" class="form-control" value="<?= htmlspecialchars($investimento['nome_investimento']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tipo de Ativo</label>
                    <div class="input-with-icon">
                        <i class="ph ph-chart-pie-slice"></i>
                        <select name="tipo" class="form-control" required>
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

                <div class="form-group">
                    <label>Corretora / Banco</label>
                    <div class="input-with-icon">
                        <i class="ph ph-buildings"></i>
                        <input type="text" name="corretora" class="form-control" value="<?= htmlspecialchars($investimento['corretora']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Valor Atualizado (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="number" step="0.01" name="valor_aplicado" class="form-control value-input" value="<?= $investimento['valor_aplicado'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Data da Aplicação</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-blank"></i>
                        <input type="date" name="data_aplicacao" class="form-control" value="<?= $investimento['data_aplicacao'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Vencimento (Opcional)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-check"></i>
                        <input type="date" name="vencimento" class="form-control" value="<?= $investimento['vencimento'] ?>">
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px; display: flex; gap: 16px;">
                <a href="/financas/investimentos" class="btn-outline flex-1 text-center" style="display: inline-flex; justify-content: center;">
                    <i class="ph ph-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1 text-center" style="display: inline-flex; justify-content: center; background-color: var(--color-emerald);">
                    <i class="ph ph-floppy-disk"></i> Atualizar Saldo e Salvar
                </button>
            </div>
        </form>
    </div>
</div>