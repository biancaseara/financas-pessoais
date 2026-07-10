<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Personalize sua Experiência' ?> | PREDITIV.IA</title>
    <link rel="stylesheet" href="/financas/public/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card" style="max-width: 500px; width: 100%;">
    <div class="auth-header">
      <h2 class="auth-title">Vamos personalizar sua experiência</h2>
      <div class="progress-bar mt-3">
        <div id="onboarding-progress" style="width: 16.6%; background-color: var(--color-ia-purple);"></div>
      </div>
    </div>

    <form id="onboardingForm" action="/financas/onboarding/store" method="POST" class="auth-form">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

      <div class="onboarding-step active" data-step="1">
        <label for="sentimento_dinheiro">1. Como você se sente em relação ao seu dinheiro hoje?</label>
        <select name="sentimento_dinheiro" id="sentimento_dinheiro" class="form-control" required>
          <option value="" disabled selected>Selecione uma opção...</option>
          <option value="Ansioso">Ansioso e preocupado</option>
          <option value="Tranquilo">Tranquilo, mas quero melhorar</option>
          <option value="No controle">Totalmente no controle</option>
        </select>
        <div class="form-actions" style="display: flex; justify-content: flex-end; margin-top: 24px;">
          <button type="button" class="btn-primary btn-next">Próxima <i class="ph ph-arrow-right"></i></button>
        </div>
      </div>

      <div class="onboarding-step" data-step="2" style="display: none;">
        <label for="conhecimento_financeiro">2. Qual seu nível de conhecimento financeiro?</label>
        <select name="conhecimento_financeiro" id="conhecimento_financeiro" class="form-control" required>
          <option value="" disabled selected>Selecione uma opção...</option>
          <option value="Iniciante">Iniciante (sei muito pouco)</option>
          <option value="Intermediario">Intermediário (sei o básico)</option>
          <option value="Avancado">Avançado (invisto e controlo bem)</option>
        </select>
        <div class="form-actions" style="display: flex; justify-content: space-between; margin-top: 24px;">
          <button type="button" class="btn-outline btn-prev"><i class="ph ph-arrow-left"></i> Voltar</button>
          <button type="button" class="btn-primary btn-next">Próxima <i class="ph ph-arrow-right"></i></button>
        </div>
      </div>

      <div class="onboarding-step" data-step="3" style="display: none;">
        <label for="renda_mensal">3. Qual é a sua faixa de renda mensal atual?</label>
        <select name="renda_mensal" id="renda_mensal" class="form-control" required>
          <option value="" disabled selected>Selecione uma opção...</option>
          <option value="Ate 2000">Até R$ 2.000</option>
          <option value="2001 a 5000">De R$ 2.001 a R$ 5.000</option>
          <option value="5001 a 10000">De R$ 5.001 a R$ 10.000</option>
          <option value="Acima de 10000">Acima de R$ 10.000</option>
        </select>
        <div class="form-actions" style="display: flex; justify-content: space-between; margin-top: 24px;">
          <button type="button" class="btn-outline btn-prev"><i class="ph ph-arrow-left"></i> Voltar</button>
          <button type="button" class="btn-primary btn-next">Próxima <i class="ph ph-arrow-right"></i></button>
        </div>
      </div>

      <div class="onboarding-step" data-step="4" style="display: none;">
        <label for="tipo_renda">4. Qual é a sua principal fonte de renda?</label>
        <select name="tipo_renda" id="tipo_renda" class="form-control" required>
          <option value="" disabled selected>Selecione uma opção...</option>
          <option value="CLT">CLT (Carteira Assinada)</option>
          <option value="Autonomo/PJ">Autônomo ou PJ</option>
          <option value="Servidor Publico">Servidor Público</option>
          <option value="Desempregado/Estudante">Desempregado / Estudante</option>
        </select>
        <div class="form-actions" style="display: flex; justify-content: space-between; margin-top: 24px;">
          <button type="button" class="btn-outline btn-prev"><i class="ph ph-arrow-left"></i> Voltar</button>
          <button type="button" class="btn-primary btn-next">Próxima <i class="ph ph-arrow-right"></i></button>
        </div>
      </div>

      <div class="onboarding-step" data-step="5" style="display: none;">
        <label for="tem_dividas">5. Atualmente, você possui dívidas em atraso ou empréstimos ativos?</label>
        <select name="tem_dividas" id="tem_dividas" class="form-control" required>
          <option value="" disabled selected>Selecione uma opção...</option>
          <option value="Sim">Sim, possuo</option>
          <option value="Não">Não, estou livre de dívidas</option>
        </select>
        <div class="form-actions" style="display: flex; justify-content: space-between; margin-top: 24px;">
          <button type="button" class="btn-outline btn-prev"><i class="ph ph-arrow-left"></i> Voltar</button>
          <button type="button" class="btn-primary btn-next">Próxima <i class="ph ph-arrow-right"></i></button>
        </div>
      </div>

      <div class="onboarding-step" data-step="6" style="display: none;">
        <label for="objetivo_principal">6. Qual é o seu principal objetivo financeiro hoje?</label>
        <select name="objetivo_principal" id="objetivo_principal" class="form-control" required>
          <option value="" disabled selected>Selecione uma opção...</option>
          <option value="Sair das dividas">Quitar minhas dívidas</option>
          <option value="Reserva de emergencia">Criar uma reserva de emergência</option>
          <option value="Comecar a investir">Aprender a investir</option>
          <option value="Aumentar patrimonio">Aumentar meu patrimônio</option>
        </select>
        <div class="form-actions" style="display: flex; justify-content: space-between; margin-top: 24px;">
          <button type="button" class="btn-outline btn-prev"><i class="ph ph-arrow-left"></i> Voltar</button>
          <button type="submit" class="btn-primary">Finalizar <i class="ph ph-check"></i></button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('.onboarding-step');
    const btnNext = document.querySelectorAll('.btn-next');
    const btnPrev = document.querySelectorAll('.btn-prev');
    const progressBar = document.getElementById('onboarding-progress');
    
    let currentStep = 0;

    function updateView() {
        steps.forEach((step, index) => {
            if (index === currentStep) {
                step.style.display = 'block';
                setTimeout(() => step.style.opacity = '1', 50);
            } else {
                step.style.display = 'none';
                step.style.opacity = '0';
            }
        });

        const progressPercentage = ((currentStep + 1) / steps.length) * 100;
        progressBar.style.width = progressPercentage + '%';
    }

    btnNext.forEach(button => {
        button.addEventListener('click', () => {
            const currentInput = steps[currentStep].querySelector('select, input');
            if(currentInput && !currentInput.value) {
                alert('Por favor, responda a pergunta antes de continuar.');
                return;
            }

            if (currentStep < steps.length - 1) {
                currentStep++;
                updateView();
            }
        });
    });

    btnPrev.forEach(button => {
        button.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateView();
            }
        });
    });

    steps.forEach(step => step.style.transition = 'opacity 0.3s ease');
    updateView();
});
</script>
</body>
</html>