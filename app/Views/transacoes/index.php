<div class="transactions-container">

    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-plus-circle" style="margin-right: 8px;"></i> <?= htmlspecialchars('Nova Transação', ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <form action="/financas/transacoes/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group type-toggle">
                <label class="radio-card expense">
                    <input type="radio" name="tipo_transacao" value="Saida" checked>
                    <div class="radio-content">
                        <i class="ph ph-arrow-circle-down"></i>
                        <span>Saída</span>
                    </div>
                </label>
                <label class="radio-card income">
                    <input type="radio" name="tipo_transacao" value="Entrada">
                    <div class="radio-content">
                        <i class="ph ph-arrow-circle-up"></i>
                        <span>Entrada</span>
                    </div>
                </label>
                <label class="radio-card transfer">
                    <input type="radio" name="tipo_transacao" value="Transferencia">
                    <div class="radio-content">
                        <i class="ph ph-arrows-left-right"></i>
                        <span>Transferência</span>
                    </div>
                </label>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Data</label>
                    <div class="input-with-icon">
                        <i class="ph ph-calendar-blank"></i>
                        <input type="date" name="data_transacao" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="form-group" id="linha_metodo">
                    <label>Forma de Pagamento</label>
                    <div class="input-with-icon">
                        <i class="ph ph-wallet"></i>
                        <select name="forma_pagamento" id="forma_pagamento" class="form-control">
                            <option value="Débito">Débito</option>
                            <option value="Pix">Pix</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Dinheiro">Dinheiro Vivo</option>
                            <option value="Crédito">Cartão de Crédito</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_cartao_container" style="display: none;">
                    <label>Cartão de Crédito</label>
                    <div class="input-with-icon">
                        <i class="ph ph-credit-card"></i>
                        <select name="id_cartao" id="box_cartao" class="form-control">
                            <option value="" disabled selected>Escolha o Cartão</option>
                            <?php if (!empty($cartoes)): ?>
                                <?php foreach ($cartoes as $cartao): ?>
                                    <option value="<?= $cartao['id_cartao'] ?>">
                                        <?= htmlspecialchars($cartao['nome_cartao'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Nenhum cartão cadastrado</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_parcelas_container" style="display: none;">
                    <label>Parcelas</label>
                    <div class="input-with-icon">
                        <i class="ph ph-list-numbers"></i>
                        <select name="parcelas" id="box_parcelas" class="form-control">
                            <?php for($i=1; $i<=24; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?>x</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_conta_container">
                    <label>Conta Origem</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <select name="id_conta" id="box_conta" class="form-control" required>
                            <option value="" disabled selected>Conta Origem</option>
                            <?php foreach ($contas as $c): ?>
                                <option value="<?= $c['id_conta'] ?>">
                                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?> (R$ <?= number_format($c['saldo_inicial'], 2, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_destino_container" style="display: none;">
                    <label>Conta Destino</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <select name="id_conta_destino" id="box_destino" class="form-control">
                            <option value="" disabled selected>Conta Destino</option>
                            <?php foreach ($contas as $c): ?>
                                <option value="<?= $c['id_conta'] ?>">
                                    <?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="box_categoria_container">
                    <label>Categoria</label>
                    <div class="input-with-icon">
                        <i class="ph ph-tag"></i>
                        <select name="id_categoria" id="box_categoria" class="form-control" required>
                            <option value="" disabled selected>Escolha a Categoria</option>
                            <optgroup label="Despesas (Saídas)">
                            <?php foreach ($categorias as $cat): ?>
                                <?php if ($cat['tipo'] == 'D'): ?>
                                    <option value="<?= $cat['id_categoria'] ?>">
                                        <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Receitas (Entradas)">
                            <?php foreach ($categorias as $cat): ?>
                                <?php if ($cat['tipo'] == 'R'): ?>
                                    <option value="<?= $cat['id_categoria'] ?>">
                                        <?= htmlspecialchars($cat['nome_categoria'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="form-group value-group">
                    <label>Valor (R$)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-currency-dollar"></i>
                        <input type="text" name="valor" class="form-control value-input" placeholder="0,00" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Descrição</label>
                    <div class="input-with-icon">
                        <i class="ph ph-text-aa"></i>
                        <input type="text" name="descricao" class="form-control" placeholder="Ex: Mercado, Conta de Luz..." required>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary w-full"><i class="ph ph-check-circle"></i> Registrar Transação</button>
            </div>
        </form>
    </div>

    <!-- Modal de Importação de Extrato -->
    <div id="modalImportacao" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="fecharModalImportacao()">&times;</span>
            <h3 style="margin-bottom: 5px;">Importação Inteligente</h3>
            <p class="text-muted" style="margin-bottom: 20px;">Cole o texto bruto do extrato. A IA fará o resto.</p>

            <form id="formImportacao">
                <div class="form-group type-toggle" style="margin-bottom: 20px;">
                    <label class="radio-card income">
                        <input type="radio" name="destino_importacao" value="conta" checked onclick="alternarDestinoImportacao('conta')">
                        <div class="radio-content">
                            <i class="ph ph-bank"></i>
                            <span>Conta</span>
                        </div>
                    </label>
                    <label class="radio-card expense">
                        <input type="radio" name="destino_importacao" value="cartao" onclick="alternarDestinoImportacao('cartao')">
                        <div class="radio-content">
                            <i class="ph ph-credit-card"></i>
                            <span>Cartão</span>
                        </div>
                    </label>
                </div>

                <div class="form-group" id="divContaImportacao">
                    <label>Conta Destino</label>
                    <div class="input-with-icon">
                        <i class="ph ph-bank"></i>
                        <select id="contaImportacao" class="form-control">
                            <option value="">Selecione a Conta...</option>
                            <?php foreach ($contas as$c): ?>
                                <option value="<?= $c['id_conta'] ?>"><?= htmlspecialchars($c['nome_banco'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="divCartaoImportacao" style="display: none;">
                    <label>Cartão Destino</label>
                    <div class="input-with-icon">
                        <i class="ph ph-credit-card"></i>
                        <select id="cartaoImportacao" class="form-control">
                            <option value="">Selecione o Cartão...</option>
                            <?php if (!empty($cartoes)): foreach ($cartoes as$cartao): ?>
                                <option value="<?= $cartao['id_cartao'] ?>"><?= htmlspecialchars($cartao['nome_cartao'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width" style="margin-top: 15px;">
                    <label>Arquivo do Extrato (CSV)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-file-csv"></i>
                        <input type="file" id="arquivoExtrato" name="arquivo_extrato" class="form-control" accept=".csv" required style="padding: 10px;">
                    </div>
                </div>
                <div id="statusImportacao" class="status-msg form-group" style="display: none; margin-top: 15px;">
                    <p style="display: flex; align-items: center; gap: 8px; font-weight: 500; margin: 0;">
                        <i class="ph ph-gear spinning-icon" style="font-size: 1.5rem; color: var(--primary-color);"></i> 
                        A IA está processando os dados...
                    </p>
                </div>

                <div class="form-actions" style="margin-top: 20px;">
                    <button type="button" class="btn-primary w-full" onclick="enviarParaIA()">
                        <i class="ph ph-magic-wand"></i> Analisar com IA
                    </button>
                </div>
            </form>
            
            <div id="areaRevisao" style="display: none; margin-top: 30px;">
                <h4 style="margin-bottom: 10px;">Revisão das Transações</h4>
                <div style="overflow-x: auto;">
                    <table class="tabela-extrato" id="tabelaRevisao">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Categoria</th>
                                <th>Forma Pag.</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="form-actions" style="margin-top: 20px;">
                    <button type="button" class="btn-primary w-full" style="background-color: var(--success-color, #10b981);" onclick="salvarImportacaoNoBanco()">
                        <i class="ph ph-check-circle"></i> Confirmar e Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-container">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <h4 style="margin: 0;"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h4>
            
            <button type="button" class="btn-primary" onclick="abrirModalImportacao()" style="width: auto; padding: 8px 16px;">
                <i class="ph ph-upload-simple"></i> Importar Extrato
            </button>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Origem</th>
                        <th>Forma</th>
                        <th>Categoria</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($transacoes) > 0): ?>
                        <?php foreach ($transacoes as $item): ?>
                            <?php 
                                $dataBr = date('d/m/Y', strtotime($item['data_transacao']));
                                
                                if ($item['tipo_transacao'] == 'Entrada') {
                                    $corValor = 'positive font-medium';
                                    $sinal = '+ ';
                                } elseif ($item['tipo_transacao'] == 'Saida') {
                                    $corValor = 'negative font-medium';
                                    $sinal = '- ';
                                } else {
                                    $corValor = 'font-medium style="color: var(--color-ia-purple);"';
                                    $sinal = '';
                                }

                                $origem = $item['nome_banco'] ?? 'Fatura de Cartão';
                                $iconeOrigem = $item['nome_banco'] ? 'ph-bank' : 'ph-credit-card';
                                $formaPagamento = $item['forma_pagamento'] ?? 'Outros';
                            ?>
                            <tr>
                                <td class="text-secondary"><?= $dataBr ?></td>
                                <td>
                                    <span class="account-tag"><i class="ph <?= $iconeOrigem ?>"></i> <?= htmlspecialchars($origem, ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="font-medium text-secondary">
                                    <?= htmlspecialchars($formaPagamento, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <span class="badge"><?= htmlspecialchars($item['nome_categoria'] ?? 'Transferência', ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="font-medium"><?= htmlspecialchars($item['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="<?= $corValor ?>">
                                    <?= $sinal ?>R$ <?= number_format($item['valor'], 2, ',', '.') ?>
                                </td>
                                <td class="text-right">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="/financas/transacoes/edit/<?= $item['id_transacao'] ?>" class="icon-btn-sm" title="Editar">
                                            <i class="ph ph-pencil-simple"></i>
                                        </a>
                                        
                                        <form action="/financas/transacoes/delete/<?= $item['id_transacao'] ?>" method="POST" style="margin: 0;">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                            <button type="submit" class="icon-btn-sm danger" title="Excluir" onclick="return confirm('Apagar transação e reverter saldos?');">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; padding: 32px; color: var(--text-secondary);">Nenhuma transação registrada.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (isset($total_paginas) && $total_paginas > 1): ?>
        <div class="pagination-container" style="display: flex; justify-content: center; gap: 8px; padding: 24px 16px; border-top: 1px solid var(--border-color);">
            
            <?php if ($pagina_atual > 1): ?>
                <a href="?pagina=<?= $pagina_atual - 1 ?>" class="btn-outline" style="padding: 6px 12px; border-radius: 6px; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                    <i class="ph ph-caret-left"></i> Anterior
                </a>
            <?php endif; ?>

            <div style="display: flex; gap: 4px; align-items: center;">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>" class="<?= $i == $pagina_atual ? 'btn-primary' : 'btn-outline' ?>" style="padding: 6px 12px; border-radius: 6px; text-decoration: none; min-width: 36px; text-align: center;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>

            <?php if ($pagina_atual < $total_paginas): ?>
                <a href="?pagina=<?= $pagina_atual + 1 ?>" class="btn-outline" style="padding: 6px 12px; border-radius: 6px; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                    Próxima <i class="ph ph-caret-right"></i>
                </a>
            <?php endif; ?>
            
        </div>
        <?php endif; ?>        
    </div>
</div>

<script>
$(document).ready(function() {
    $('input[name="tipo_transacao"]').change(function() {
        let tipo = $(this).val();
        
        if (tipo == 'Transferencia') {
            $('#box_destino_container').show();
            $('#box_destino').prop('required', true);
            $('#box_categoria_container').hide();
            $('#box_categoria').prop('required', false).val('');
            $('#linha_metodo').hide(); 
            $('#forma_pagamento').val('Outros').trigger('change');
        } else if (tipo == 'Entrada') {
            $('#box_destino_container').hide();
            $('#box_destino').prop('required', false).val('');
            $('#box_categoria_container').show();
            $('#box_categoria').prop('required', true);
            $('#linha_metodo').show();
            
            if($('#forma_pagamento').val() == 'Crédito') {
                $('#forma_pagamento').val('Pix').trigger('change');
            }
        } else {
            // Se for Saída
            $('#box_destino_container').hide();
            $('#box_destino').prop('required', false).val('');
            $('#box_categoria_container').show();
            $('#box_categoria').prop('required', true);
            $('#linha_metodo').show();
        }
    });

    // Lógica para Forma de Pagamento
    $('#forma_pagamento').change(function() {
        if ($(this).val() == 'Crédito') {
            $('#box_cartao_container').show();
            $('#box_cartao').prop('required', true);
            $('#box_parcelas_container').show();
            $('#box_conta_container').hide();
            $('#box_conta').prop('required', false).val('');
        } else {
            $('#box_cartao_container').hide();
            $('#box_cartao').prop('required', false).val('');
            $('#box_parcelas_container').hide();
            $('#box_parcelas').val('1');
            $('#box_conta_container').show();
            $('#box_conta').prop('required', true);
        }
    });
});

let transacoesExtraidas = [];
let destinoSelecionado = 'conta';

function abrirModalImportacao() {
    document.getElementById('modalImportacao').style.display = 'block';
}

function fecharModalImportacao() {
    document.getElementById('modalImportacao').style.display = 'none';
    document.getElementById('areaRevisao').style.display = 'none';
    document.getElementById('arquivoExtrato').value = '';
    document.getElementById('statusImportacao').style.display = 'none';
}

function alternarDestinoImportacao(tipo) {
    destinoSelecionado = tipo;
    if (tipo === 'conta') {
        document.getElementById('divContaImportacao').style.display = 'block';
        document.getElementById('divCartaoImportacao').style.display = 'none';
    } else {
        document.getElementById('divContaImportacao').style.display = 'none';
        document.getElementById('divCartaoImportacao').style.display = 'block';
    }
}

async function enviarParaIA() {
    const arquivoInput = document.getElementById('arquivoExtrato');
    const idConta = document.getElementById('contaImportacao').value;
    const idCartao = document.getElementById('cartaoImportacao').value;

    if (destinoSelecionado === 'conta' && !idConta) { alert('Selecione a conta.'); return; }
    if (destinoSelecionado === 'cartao' && !idCartao) { alert('Selecione o cartão.'); return; }
    if (!arquivoInput.files || arquivoInput.files.length === 0) { alert('Selecione o arquivo CSV do extrato.'); return; }

    document.getElementById('statusImportacao').style.display = 'block';
    document.getElementById('areaRevisao').style.display = 'none';

    try {
        const formData = new FormData();
        formData.append('arquivo_extrato', arquivoInput.files[0]);

        const response = await fetch('/financas/ia/processarExtrato', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) throw new Error('Falha no servidor.');

        transacoesExtraidas = await response.json();
        
        if (transacoesExtraidas.length === 0) {
            alert("Não foi possível processar o arquivo.");
            document.getElementById('statusImportacao').style.display = 'none';
            return;
        }

        transacoesExtraidas = transacoesExtraidas.map(t => ({
            ...t, 
            id_conta: destinoSelecionado === 'conta' ? idConta : null,
            id_cartao: destinoSelecionado === 'cartao' ? idCartao : null
        }));

        renderizarTabelaRevisao(transacoesExtraidas);
        document.getElementById('statusImportacao').style.display = 'none';
        document.getElementById('areaRevisao').style.display = 'block';

    } catch (error) {
        alert('Erro ao processar o extrato. Verifique o console.');
        document.getElementById('statusImportacao').style.display = 'none';
        console.error(error);
    }
}

function renderizarTabelaRevisao(transacoes) {
    const tbody = document.querySelector('#tabelaRevisao tbody');
    tbody.innerHTML = '';

    transacoes.forEach(t => {
        const tr = document.createElement('tr');
        const corValor = t.tipo_transacao === 'Saida' ? 'color: #ef4444;' : 'color: #10b981;';
        
        tr.innerHTML = `
            <td>${t.data}</td>
            <td>${t.descricao}</td>
            <td style="${corValor}; font-weight: 600;">R$ ${parseFloat(t.valor).toFixed(2).replace('.', ',')}</td>
            <td>${t.categoria}</td>
            <td>${t.forma_pagamento}</td>
        `;
        tbody.appendChild(tr);
    });
}

async function salvarImportacaoNoBanco() {
    if (transacoesExtraidas.length === 0) return;
    try {
        const response = await fetch('/financas/transacoes/salvarImportacao', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ transacoes: transacoesExtraidas })
        });
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Erro ao salvar no banco de dados.');
        }
    } catch (error) {
        alert('Erro de comunicação.');
    }
}
</script>