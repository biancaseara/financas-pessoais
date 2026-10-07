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
                    <label style="display: flex; justify-content: space-between; align-items: center;">
                        Tipo de Ativo
                        <button type="button" onclick="document.getElementById('modalNovoTipo').style.display='block'" style="background: none; border: none; color: var(--primary-color); cursor: pointer; font-size: 0.85rem; font-weight: 600;">+ Criar Novo</button>
                    </label>
                    <div class="input-with-icon">
                        <i class="ph ph-chart-pie-slice"></i>
                        <select name="tipo" class="form-control" required>
                            <option value="" disabled selected>Qual o tipo deste ativo?</option>
                            <?php 
                            $grupoAtual = '';
                            foreach ($tipos as $t): 
                                if ($grupoAtual != $t['categoria_grupo']) {
                                    if ($grupoAtual != '') echo '</optgroup>';
                                    echo '<optgroup label="'. htmlspecialchars($t['categoria_grupo']) .'">';
                                    $grupoAtual = $t['categoria_grupo'];
                                }
                            ?>
                                <option value="<?= htmlspecialchars($t['nome_tipo']) ?>" <?= ($investimento['tipo'] == $t['nome_tipo']) ? 'selected' : '' ?>><?= htmlspecialchars($t['nome_tipo']) ?></option>
                            <?php endforeach; if ($grupoAtual != '') echo '</optgroup>'; ?>
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

    <div id="modalNovoTipo" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 400px;">
            <span class="close" onclick="document.getElementById('modalNovoTipo').style.display='none'">&times;</span>
            <h3 style="margin-bottom: 15px;">Nova Categoria de Ativo</h3>
            <form action="/financas/investimentos/storeTipo" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="form-group">
                    <label>Nome da Categoria</label>
                    <input type="text" name="nome_tipo" class="form-control" placeholder="Ex: Caixinhas do Nubank" required style="padding: 10px;">
                </div>
                
                <div class="form-group" style="margin-top: 15px;">
                    <label>Grupo de Risco</label>
                    <select name="grupo" class="form-control" required style="padding: 10px;">
                        <option value="Renda Fixa">Renda Fixa (Baixo Risco)</option>
                        <option value="Renda Variável">Renda Variável (Alto Risco)</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary w-full" style="margin-top: 20px;">Salvar Categoria</button>
            </form>
        </div>
    </div>
</div>