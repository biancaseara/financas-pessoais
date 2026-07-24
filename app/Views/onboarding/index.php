<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> - Preditiv.ia</title>
    <!-- O caminho do CSS foi restaurado exatamente para o que funciona no seu projeto -->
    <link rel="stylesheet" href="/financas/public/css/style.css"> 
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
        .grid-options.full {
            grid-template-columns: 1fr;
        }
        .radio-card input:checked + .radio-content {
            border-color: var(--color-ia-purple);
            background-color: var(--color-ia-glow);
            color: var(--color-ia-purple);
        }
        /* Estilos do Wizard (Passo a Passo) */
        .step-container {
            display: none;
            animation: fadeIn 0.4s ease-in-out;
        }
        .step-container.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
            margin-top: 16px;
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
                    <h2 class="auth-title" style="margin: 0; font-size: 20px;">Personalize sua Experiência</h2>
                    <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">Para a IA moldar o sistema para você.</p>
                </div>
                <div class="step-indicator" id="step-counter">Passo 1 de 2</div>
            </div>

            <form action="/financas/onboarding/store" method="POST" id="onboardingForm">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- ================= PASSO 1 (3 Perguntas) ================= -->
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
                        <button type="button" class="btn-primary" style="width: 100%; justify-content: center;" onclick="nextStep()">
                            Próximo Passo <i class="ph ph-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- ================= PASSO 2 (3 Perguntas) ================= -->
                <div class="step-container" id="step2">
                    
                    <label class="form-label">4. Qual a sua renda mensal aproximada?</label>
                    <div class="grid-options">
                        <label class="radio-card"><input type="radio" name="renda_mensal" value="Até 2000"><div class="radio-content">Até R$ 2.000</div></label>
                        <label class="radio-card"><input type="radio" name="renda_mensal" value="2001 a 5000"><div class="radio-content">Até R$ 5.000</div></label>
                        <label class="radio-card"><input type="radio" name="renda_mensal" value="5001 a 10000"><div class="radio-content">Até R$ 10.000</div></label>
                        <label class="radio-card"><input type="radio" name="renda_mensal" value="Acima de 10000"><div class="radio-content">Acima de R$ 10.000</div></label>
                    </div>

                    <label class="form-label">5. Qual é o tipo da sua renda principal?</label>
                    <div class="grid-options">
                        <label class="radio-card"><input type="radio" name="tipo_renda" value="CLT"><div class="radio-content">CLT</div></label>
                        <label class="radio-card"><input type="radio" name="tipo_renda" value="Autônomo"><div class="radio-content">Autônomo / PJ</div></label>
                        <label class="radio-card"><input type="radio" name="tipo_renda" value="Servidor"><div class="radio-content">Servidor Público</div></label>
                        <label class="radio-card"><input type="radio" name="tipo_renda" value="Empresário"><div class="radio-content">Outros</div></label>
                    </div>

                    <label class="form-label">6. Qual o seu principal objetivo com o Preditiv.ia?</label>
                    <div class="grid-options">
                        <label class="radio-card"><input type="radio" name="objetivo_principal" value="Sair das dívidas"><div class="radio-content">Sair das dívidas</div></label>
                        <label class="radio-card"><input type="radio" name="objetivo_principal" value="Controlar gastos"><div class="radio-content">Controlar gastos</div></label>
                        <label class="radio-card"><input type="radio" name="objetivo_principal" value="Reserva de emergencia"><div class="radio-content">Criar reserva</div></label>
                        <label class="radio-card"><input type="radio" name="objetivo_principal" value="Investir"><div class="radio-content">Aprender a investir</div></label>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn-outline" style="flex: 1; justify-content: center;" onclick="prevStep()">
                            <i class="ph ph-arrow-left"></i> Voltar
                        </button>
                        <button type="submit" class="btn-primary" style="flex: 2; justify-content: center;">
                            Finalizar e Acessar <i class="ph ph-rocket-launch"></i>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- SCRIPT DO WIZARD -->
    <script>
        function nextStep() {
            // Validação simples
            const groups = ['sentimento_dinheiro', 'conhecimento_financeiro', 'tem_dividas'];
            let isValid = true;

            groups.forEach(group => {
                const checked = document.querySelector(`input[name="${group}"]:checked`);
                if (!checked) isValid = false;
            });

            if (!isValid) {
                alert("Por favor, selecione uma opção para cada pergunta antes de avançar.");
                return;
            }

            // Alterna a tela
            document.getElementById('step1').classList.remove('active');
            document.getElementById('step2').classList.add('active');
            document.getElementById('step-counter').innerText = 'Passo 2 de 2';
        }

        function prevStep() {
            document.getElementById('step2').classList.remove('active');
            document.getElementById('step1').classList.add('active');
            document.getElementById('step-counter').innerText = 'Passo 1 de 2';
        }

        // Validação extra no submit final
        document.getElementById('onboardingForm').addEventListener('submit', function(e) {
            const groups = ['renda_mensal', 'tipo_renda', 'objetivo_principal'];
            let isValid = true;

            groups.forEach(group => {
                const checked = document.querySelector(`input[name="${group}"]:checked`);
                if (!checked) isValid = false;
            });

            if (!isValid) {
                e.preventDefault();
                alert("Por favor, responda todas as perguntas para finalizar.");
            }
        });
    </script>
</body>
</html>