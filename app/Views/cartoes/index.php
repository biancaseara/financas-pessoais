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
                <div class="card credit-card-item" style="border-top: 4px solid <?= htmlspecialchars($c['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>;">
                    
                    <div class="cc-header">
                        <div class="cc-title">
                            <i class="ph-fill ph-credit-card" style="color: <?= htmlspecialchars($c['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>;"></i>
                            <h3><?= htmlspecialchars($c['nome_cartao'], ENT_QUOTES, 'UTF-8') ?></h3>
                        </div>
                    </div>
                    
                    <div class="cc-body">
                        <span class="cc-label">Limite Total</span>
                        <h4 class="cc-limit">R$ <?= number_format($c['limite_total'], 2, ',', '.') ?></h4>
                        
                        <div class="cc-dates">
                            <div class="date-info">
                                <span>Fechamento</span>
                                <strong>Dia <?= htmlspecialchars($c['dia_fechamento'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <div class="date-info">
                                <span>Vencimento</span>
                                <strong>Dia <?= htmlspecialchars($c['dia_vencimento'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                        </div>
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