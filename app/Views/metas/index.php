<h2><?= $titulo ?></h2>

<form action="/financas/metas/store" method="POST" class="d-flex flex-column" style="margin-bottom: 20px;">
    <div class="d-flex">
        <input type="text" name="titulo_meta" placeholder="Objetivo (Ex: Comprar Carro)" required style="flex-grow:1;">
        <input type="date" name="data_limite" required title="Data Limite">
    </div>
    <div class="d-flex" style="margin-top:10px;">
        <input type="number" step="0.01" name="valor_objetivo" placeholder="Quanto precisa juntar? (R$)" required style="flex-grow:1;">
        <input type="number" step="0.01" name="valor_atual" placeholder="Quanto já tem guardado? (R$)" required style="flex-grow:1;">
    </div>
    <div class="d-flex" style="margin-top:10px;">
        <button type="submit">Criar Meta</button>
    </div>
</form>

<hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ccc;">

<?php if (count($metas) > 0): ?>
    <?php foreach ($metas as $item): ?>
        <?php
            $objetivo = $item['valor_objetivo'];
            $atual = $item['valor_atual'];
            $porcentagem = ($objetivo > 0) ? ($atual / $objetivo) * 100 : 0;
            $larguraBarra = ($porcentagem > 100) ? 100 : $porcentagem;
            $dataBr = date('d/m/Y', strtotime($item['data_limite']));
        ?>
        <div style="background: white; border: 1px solid #ccc; margin-bottom: 15px; padding: 15px; border-radius: 5px;">
            <div style="display:flex; justify-content:space-between; margin-bottom: 10px;">
                <b style="font-size: 1.1em;">🎯 <?= htmlspecialchars($item['titulo_meta']) ?></b>
                <span style="color: #666; font-size: 0.9em;">Data Limite: <?= $dataBr ?></span>
            </div>

            <p style="margin-bottom: 10px; font-weight: bold; color: #333;">
                R$ <?= number_format($atual, 2, ',', '.') ?> de R$ <?= number_format($objetivo, 2, ',', '.') ?>
            </p>

            <div style="background-color: #eee; width: 100%; height: 20px; border-radius: 10px; overflow: hidden; margin-bottom: 15px;">
                <div style="background-color: <?= $porcentagem >= 100 ? '#28a745' : '#007BFF' ?>; width: <?= $larguraBarra ?>%; height: 100%; text-align: center; color: white; font-size: 12px; line-height: 20px; font-weight: bold;">
                    <?= number_format($porcentagem, 1) ?>%
                </div>
            </div>

            <div class="d-flex">
                <a href="/financas/metas/edit/<?= $item['id_meta'] ?>" style="padding: 8px 12px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">Editar</a>
                
                <form action="/financas/metas/delete/<?= $item['id_meta'] ?>" method="POST" style="margin: 0;">
                    <button type="submit" style="background: #DC3545; padding: 8px 12px; font-size: 14px; border: none; cursor: pointer;" onclick="return confirm('Tem certeza que deseja desistir desta meta?');">Desistir</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nenhuma meta definida ainda.</p>
<?php endif; ?>