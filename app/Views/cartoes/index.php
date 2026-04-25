<h2>Cadastrar Novo Cartão</h2>

<form action="/financas/cartoes/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <div class="d-flex" style="gap: 10px;">
        <input type="text" name="nome_cartao" placeholder="Nome do Cartão (Ex: Nubank)" required style="flex-grow: 1; padding: 10px;">
        <input type="text" name="limite_total" placeholder="Limite Total (Ex: 5000,00)" required style="flex-grow: 1; padding: 10px;">
    </div>

    <div class="d-flex" style="margin-top: 15px; gap: 10px;">
        <input type="number" name="dia_fechamento" placeholder="Dia de Fechamento (Ex: 25)" min="1" max="31" required style="flex-grow: 1; padding: 10px;">
        <input type="number" name="dia_vencimento" placeholder="Dia de Vencimento (Ex: 5)" min="1" max="31" required style="flex-grow: 1; padding: 10px;">
        <input type="color" name="cor_identificacao" value="#8A05BE" style="padding: 5px; height: 42px; cursor: pointer;" title="Cor do Cartão">
    </div>

    <div class="d-flex" style="margin-top: 15px;">
        <button type="submit" style="padding: 10px 15px; cursor: pointer;">Cadastrar Cartão</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

<div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;">
    <?php if (count($cartoes) > 0): ?>
        <?php foreach ($cartoes as $c): ?>
            <div style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px; width: 300px; border-top: 5px solid <?= htmlspecialchars($c['cor_identificacao'], ENT_QUOTES, 'UTF-8') ?>;">
                <h3 style="margin-top: 0;">💳 <?= htmlspecialchars($c['nome_cartao'], ENT_QUOTES, 'UTF-8') ?></h3>
                
                <p style="margin: 5px 0; color: #555;">
                    <strong>Limite Total:</strong> R$ <?= number_format($c['limite_total'], 2, ',', '.') ?>
                </p>
                <p style="margin: 5px 0; color: #555; font-size: 14px;">
                    Fechamento: Dia <?= htmlspecialchars($c['dia_fechamento'], ENT_QUOTES, 'UTF-8') ?> | Vencimento: Dia <?= htmlspecialchars($c['dia_vencimento'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <a href="/financas/cartoes/edit/<?= $c['id_cartao'] ?>" style="padding: 5px 10px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; text-align: center; flex-grow: 1;">Editar</a>
                    
                    <form action="/financas/cartoes/delete/<?= $c['id_cartao'] ?>" method="POST" style="margin: 0; display: inline-flex; flex-grow: 1;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" style="width: 100%; background: #DC3545; color: white; padding: 5px 10px; font-size: 12px; border: none; cursor: pointer; border-radius: 4px;" onclick="return confirm('Excluir este cartão e todas as faturas atreladas a ele?');">Excluir</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: #666; font-style: italic;">Você ainda não possui cartões de crédito cadastrados.</p>
    <?php endif; ?>
</div>