<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - Preditiv.ia</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css"> 
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .form-label {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 12px;
            display: block;
            color: var(--text-primary);
        }
        .grid-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 32px;
        }
        .radio-card input:checked + .radio-content {
            border-color: var(--color-ia-purple);
            background-color: var(--color-ia-glow);
            color: var(--color-ia-purple);
        }
        .step-container {
            display: none;
            animation: fadeIn 0.4s ease-in-out;
        }
        .step-container.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .wizard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }
        .step-indicator {
            font-size: 13px;
            font-weight: 600;
            color: var(--color-ia-purple);
            background: var(--color-ia-glow);
            padding: 4px 12px;
            border-radius: 12px;
        }
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper" style="padding: 40px 20px;">
        <div class="auth-card" style="max-width: 650px; width: 100%;">
            <div class="auth-header" style="margin-bottom: 16px;">
                <div class="auth-logo-container">
                    <i class="ph-fill ph-sparkle" style="color: var(--color-ia-purple); font-size: 32px;"></i>
                    <h1 class="logo-text">Preditiv<span class="highlight">.ia</span></h1>
                </div>
            </div>

            <div class="wizard-header">
                <div>
                    <h2 class="auth-title" id="step-title" style="margin: 0; font-size: 20px;">Perfil Financeiro</h2>
                    <p id="step-desc" style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">Como você lida com o seu dinheiro hoje?</p>
                </div>
                <div class="step-indicator" id="step-counter">Passo 1 de 4</div>
            </div>

            <form action="/financas/onboarding/store" method="POST" id="onboardingForm">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- PASSO 1: PERFIL IA -->
                <div class="step-container active" id="step1">
                    
                    <label class="form-label">1. Como você se sente em relação ao seu dinheiro hoje?</label>
                    <div class="grid-options">
                        <label class="radio-card"><input type="radio" name="sentimento_dinheiro" value="Sempre falta dinheiro no fim do mês"><div class="radio-content">Sempre falta</div></label>
                        <label class="radio-card"><input type="radio" name="sentimento_dinheiro" value="Consigo pagar as contas, mas não sobra nada"><div class="radio-content">Não sobra nada</div></label>
                        <label class="radio-card"><input type="radio" name="sentimento_dinheiro" value="Sobra um pouco, mas não sei investir"><div class="radio-content">Sobra, mas não invisto</div></label>
                        <label class="radio-card"><input type="radio" name="sentimento_dinheiro" value="Tenho controle e invisto todo mês"><div class="radio-content">Tenho controle e invisto</div></label>
                    </div>

                    <label class="form-label">2. Qual o seu nível de conhecimento financeiro?</label>
                    <div class="grid-options">
                        <label class="radio-card"><input type="radio" name="conhecimento_financeiro" value="Iniciante"><div class="radio-content">Iniciante</div></label>
                        <label class="radio-card"><input type="radio" name="conhecimento_financeiro" value="Básico"><div class="radio-content">Básico</div></label>
                        <label class="radio-card"><input type="radio" name="conhecimento_financeiro" value="Intermediário"><div class="radio-content">Intermediário</div></label>
                        <label class="radio-card"><input type="radio" name="conhecimento_financeiro" value="Avançado"><div class="radio-content">Avançado</div></label>
                    </div>

                    <label class="form-label">3. Você possui dívidas atualmente?</label>
                    <div class="grid-options">
                        <label class="radio-card"><input type="radio" name="tem_dividas" value="Não"><div class="radio-content"><i class="ph ph-check-circle"></i> Não possuo</div></label>
                        <label class="radio-card"><input type="radio" name="tem_dividas" value="Sim"><div class="radio-content"><i class="ph ph-warning"></i> Sim, possuo</div></label>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn-primary" style="width: 100%; justify-content: center;" onclick="changeStep(1, 2)">
                            Próximo Passo <i class="ph ph-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- PASSO 2: PERFIL IA -->
                <div class="step-container" id="step2">
                    <label class="form-label">4. Qual a sua renda mensal líquida exata?</label>
                    <div class="form-group" style="margin-bottom: 32px;">
                        <div class="input-with-icon" style="position: relative;">
                            <span style="position: absolute; left: 16px; top: 14px; color: var(--text-secondary); font-weight: 500;">R$</span>
                            <input type="number" step="0.01" name="renda_exata" id="renda_exata" class="form-control" placeholder="0,00" style="padding-left: 45px;">
                        </div>
                    </div>

                    <label class="form-label">5. Qual é o tipo da sua renda principal?</label>
                    <div class="grid-options">
                        <label class="radio-card"><input type="radio" name="tipo_renda" value="CLT"><div class="radio-content">CLT</div></label>
                        <label class="radio-card"><input type="radio" name="tipo_renda" value="Autonomo/Empresario"><div class="radio-content">Autônomo / PJ</div></label>
                        <label class="radio-card"><input type="radio" name="tipo_renda" value="Servidor Publico"><div class="radio-content">Servidor Público</div></label>
                        <label class="radio-card"><input type="radio" name="tipo_renda" value="Desempregado/Estudante"><div class="radio-content">Estudante / Outros</div></label>
                    </div>

                    <label class="form-label">6. Qual o seu principal objetivo?</label>
                    <div class="grid-options">
                        <label class="radio-card"><input type="radio" name="objetivo_principal" value="Sair das dívidas"><div class="radio-content">Sair das dívidas</div></label>
                        <label class="radio-card"><input type="radio" name="objetivo_principal" value="Controlar gastos"><div class="radio-content">Controlar gastos</div></label>
                        <label class="radio-card"><input type="radio" name="objetivo_principal" value="Reserva de emergencia"><div class="radio-content">Criar reserva</div></label>
                        <label class="radio-card"><input type="radio" name="objetivo_principal" value="Investir"><div class="radio-content">Aprender a investir</div></label>
                        <label class="radio-card"><input type="radio" name="objetivo_principal" value="Aumentar patrimonio"><div class="radio-content">Aumentar patrimônio</div></label>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn-outline" style="flex: 1; justify-content: center;" onclick="changeStep(2, 1)">
                            <i class="ph ph-arrow-left"></i> Voltar
                        </button>
                        <button type="button" class="btn-primary" style="flex: 2; justify-content: center;" onclick="changeStep(2, 3)">
                            Próximo Passo <i class="ph ph-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- PASSO 3: PRIMEIRA CONTA -->
                <div class="step-container" id="step3">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Nome do Banco / Conta</label>
                            <div class="input-with-icon">
                                <i class="ph ph-bank"></i>
                                <input type="text" name="nome_banco" id="nome_banco" class="form-control" placeholder="Ex: Nubank, Itaú, Carteira...">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>Saldo Inicial (R$)</label>
                            <div class="input-with-icon">
                                <i class="ph ph-currency-dollar"></i>
                                <input type="text" name="saldo_inicial" id="saldo_inicial" class="form-control" placeholder="Ex: 1500,00">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>Cor de Identificação</label>
                            <div class="input-with-icon">
                                <i class="ph ph-palette"></i>
                                <input type="color" name="cor_conta" class="form-control color-picker" value="#8b5cf6">
                            </div>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn-outline" style="flex: 1; justify-content: center;" onclick="changeStep(3, 2)">
                            <i class="ph ph-arrow-left"></i> Voltar
                        </button>
                        <button type="button" class="btn-primary" style="flex: 2; justify-content: center;" onclick="changeStep(3, 4)">
                            Próximo Passo <i class="ph ph-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- PASSO 4: PRIMEIRO CARTÃO -->
                <div class="step-container" id="step4">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Nome do Cartão de Crédito</label>
                            <div class="input-with-icon">
                                <i class="ph ph-credit-card"></i>
                                <input type="text" name="nome_cartao" id="nome_cartao" class="form-control" placeholder="Ex: Nubank Ultravioleta">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>Limite Total (R$)</label>
                            <div class="input-with-icon">
                                <i class="ph ph-currency-dollar"></i>
                                <input type="text" name="limite_total" id="limite_total" class="form-control" placeholder="Ex: 5000,00">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dia de Fechamento</label>
                            <div class="input-with-icon">
                                <i class="ph ph-calendar-x"></i>
                                <input type="number" name="dia_fechamento" id="dia_fechamento" class="form-control" placeholder="Ex: 25" min="1" max="31">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dia de Vencimento</label>
                            <div class="input-with-icon">
                                <i class="ph ph-calendar-check"></i>
                                <input type="number" name="dia_vencimento" id="dia_vencimento" class="form-control" placeholder="Ex: 5" min="1" max="31">
                            </div>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn-outline" style="flex: 1; justify-content: center;" onclick="changeStep(4, 3)">
                            <i class="ph ph-arrow-left"></i> Voltar
                        </button>
                        <button type="submit" class="btn-primary" style="flex: 2; justify-content: center; background-color: var(--color-emerald);">
                            Entrar no Preditiv.ia <i class="ph ph-rocket-launch"></i>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        const stepTitles = {
            1: { title: "Perfil Financeiro", desc: "Como você lida com o seu dinheiro hoje?" },
            2: { title: "Sua Realidade", desc: "Para a IA calcular seu limite seguro." },
            3: { title: "Sua Primeira Conta", desc: "Onde o seu dinheiro vive hoje?" },
            4: { title: "Seu Primeiro Cartão", desc: "Para gerenciar suas faturas com inteligência." }
        };

        function changeStep(current, target) {
            if (target > current) {
                if (current === 1) {
                    const groups = ['sentimento_dinheiro', 'conhecimento_financeiro', 'tem_dividas'];
                    let isValid = true;
                    groups.forEach(group => {
                        if (!document.querySelector(`input[name="${group}"]:checked`)) isValid = false;
                    });
                    if (!isValid) {
                        alert("Por favor, selecione uma opção para cada pergunta antes de avançar.");
                        return;
                    }
                }
                
                if (current === 2) {
                    const groups = ['tipo_renda', 'objetivo_principal'];
                    let isValid = true;
                    groups.forEach(group => {
                        if (!document.querySelector(`input[name="${group}"]:checked`)) isValid = false;
                    });
                    const renda = document.getElementById('renda_exata').value;
                    if (!isValid || !renda) {
                        alert("Por favor, preencha o valor da sua renda e selecione todas as opções.");
                        return;
                    }
                }
                
                if (current === 3) {
                    const banco = document.getElementById('nome_banco').value;
                    const saldo = document.getElementById('saldo_inicial').value;
                    if (!banco || !saldo) {
                        alert("Preencha o nome do banco e o saldo inicial (coloque 0 se a conta estiver vazia).");
                        return;
                    }
                }
            }

            document.getElementById(`step${current}`).classList.remove('active');
            document.getElementById(`step${target}`).classList.add('active');
            
            document.getElementById('step-counter').innerText = `Passo ${target} de 4`;
            document.getElementById('step-title').innerText = stepTitles[target].title;
            document.getElementById('step-desc').innerText = stepTitles[target].desc;
        }

        document.getElementById('onboardingForm').addEventListener('submit', function(e) {
            const cartao = document.getElementById('nome_cartao').value;
            const limite = document.getElementById('limite_total').value;
            const fechamento = document.getElementById('dia_fechamento').value;
            const vencimento = document.getElementById('dia_vencimento').value;

            if (!cartao || !limite || !fechamento || !vencimento) {
                e.preventDefault();
                alert("Preencha os dados do seu primeiro cartão de crédito para podermos gerenciar seu limite e fatura.");
            }
        });
    </script>
</body>
</html>