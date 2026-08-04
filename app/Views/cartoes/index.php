<div class="transactions-container">
    
    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-credit-card" style="margin-right: 8px;"></i> Cadastrar Novo Cartão</h4>
        </div>

        <form action="/financas/cartoes/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Cartão</label>
                    <div class="input-with-icon">
                        <i class="ph ph-identification-card"></i>
                        <input type="text" name="nome_cartao" class="form-control" placeholder="Ex: Nubank" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Limite Total (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="text" name="limite_total" class="form-control" placeholder="Ex: 5000,00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dia de Fechamento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-x"></i>
                        <input type="number" name="dia_fechamento" class="form-control" placeholder="Ex: 25" min="1" max="31" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dia de Vencimento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-check"></i>
                        <input type="number" name="dia_vencimento" class="form-control" placeholder="Ex: 5" min="1" max="31" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Cor de Identificação</label>
                    <div class="input-with-icon">
                        <i class="ph ph-palette"></i>
                        <input type="color" name="cor_identificacao" class="form-control color-picker" value="#8A05BE" title="Cor do Cartão">
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-plus-circle"></i> Cadastrar Cartão
                </button>
            </div>
        </form>
    </div>

    <div class="card-header mt-3" style="margin-bottom: 16px;">
        <h4 style="font-size: 18px;"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
    </div>

    <div class="cards-grid">
        <?php if (count($cartoes) > 0): ?>
            
            <?php foreach ($cartoes as $c): ?>
                <?php
                    $faturaAtual = $cartaoModel->obterValorFaturaAtual($c['id_cartao']);
                    $limiteDisponivel = $cartaoModel->calcularLimiteDisponivel($c['limite_total'], $c['id_cartao']);
                    $limiteUsado = $c['limite_total'] - $limiteDisponivel;
                    
                    $porcentagemUso = ($c['limite_total'] > 0) ? ($limiteUsado / $c['limite_total']) * 100 : 0;
                    if ($porcentagemUso > 100) $porcentagemUso = 100;
                    
                    $corBarra = 'var(--color-emerald)';
                    if ($porcentagemUso > 75) $corBarra = 'var(--color-rose)';
                    else if ($porcentagemUso > 50) $corBarra = '#f59e0b'; // Laranja
                ?>
                
                <div class="card credit-card-item" style="border-top: 4px solid <?= htmlspecialchars($c['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>; position: relative;">
                    
                    <div class="cc-header">
                        <div class="cc-title">
                            <i class="ph-fill ph-credit-card" style="color: <?= htmlspecialchars($c['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>;"></i>
                            <h3><?= htmlspecialchars($c['nome_cartao'], ENT_QUOTES, 'UTF-8') ?></h3>
                        </div>
                    </div>
                    
                    <div class="cc-body" style="padding-bottom: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 4px;">
                            <span class="cc-label">Fatura Atual (<?= date('M/Y') ?>)</span>
                            <h4 class="cc-limit" style="font-size: 1.5rem; color: var(--color-rose);">
                                R$ <?= number_format($faturaAtual['valor_total'], 2, ',', '.') ?>
                            </h4>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 8px;">
                            <span>Disp: R$ <?= number_format($limiteDisponivel, 2, ',', '.') ?></span>
                            <span>Total: R$ <?= number_format($c['limite_total'], 2, ',', '.') ?></span>
                        </div>
                        
                        <div style="width: 100%; height: 6px; background-color: var(--surface-color); border-radius: 4px; overflow: hidden; margin-bottom: 16px;">
                            <div style="height: 100%; width: <?= $porcentagemUso ?>%; background-color: <?= $corBarra ?>; transition: width 0.3s ease;"></div>
                        </div>
                        
                        <div class="cc-dates" style="background-color: var(--surface-color); padding: 8px 12px; border-radius: 8px;">
                            <div class="date-info">
                                <span>Fechamento</span>
                                <strong>Dia <?= htmlspecialchars($c['dia_fechamento'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <div class="date-info">
                                <span>Vencimento</span>
                                <strong>Dia <?= htmlspecialchars($c['dia_vencimento'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <div class="date-info" style="text-align: right;">
                                <span>Status</span>
                                <strong style="color: <?= ($faturaAtual['status'] == 'Paga') ? 'var(--color-emerald)' : 'var(--color-ia-purple)' ?>;">
                                    <?= htmlspecialchars($faturaAtual['status'], ENT_QUOTES, 'UTF-8') ?>
                                </strong>
                            </div>
                        </div>
                        
                        <?php if ($faturaAtual['id_fatura'] && $faturaAtual['status'] !== 'Paga' && $faturaAtual['valor_total'] > 0): ?>
                            <?php if (date('d') >= $c['dia_fechamento']): ?>
                                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                                    <form action="/financas/transacoes/pagarFatura/<?= $faturaAtual['id_fatura'] ?>" method="POST" style="margin: 0; display: flex; gap: 8px;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                        <select name="id_conta_pagamento" class="form-control" style="flex: 2; padding: 6px; font-size: 0.9rem;" required>
                                            <option value="" disabled selected>Pagar com qual conta?</option>
                                            <?php foreach ($contasParaPagar as $contaPagar): ?>
                                                <option value="<?= $contaPagar['id_conta'] ?>"><?= htmlspecialchars($contaPagar['nome_banco'], ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn-primary" style="flex: 1; padding: 6px; font-size: 0.9rem; background-color: var(--color-emerald);" onclick="return confirm('Confirmar o pagamento desta fatura?');">
                                            Pagar
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div style="margin-top: 16px; text-align: center; font-size: 0.85rem; color: var(--text-secondary);">
                                    <i class="ph ph-lock-key"></i> O pagamento será liberado no dia <?= $c['dia_fechamento'] ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="cc-actions">
                        <a href="/financas/cartoes/edit/<?= $c['id_cartao'] ?>" class="btn-outline flex-1 text-center" style="justify-content: center;">
                            <i class="ph ph-pencil-simple"></i> Editar
                        </a>
                        
                        <form action="/financas/cartoes/delete/<?= $c['id_cartao'] ?>" method="POST" class="flex-1 m-0 d-flex">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn-danger w-full" onclick="return confirm('Excluir este cartão e todas as faturas atreladas a ele?');">
                                <i class="ph ph-trash"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="ph ph-credit-card"></i>
                <p>Você ainda não possui cartões de crédito cadastrados.</p>
            </div>
        <?php endif; ?>
    </div>

</div>