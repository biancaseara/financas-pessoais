<?php
// Se o valor salvo no banco for igual ao valor da option, ele marca como selecionado.
function isSel($campo, $valorEsperado, $perfil) {
    return (isset($perfil[$campo]) && $perfil[$campo] === $valorEsperado) ? 'selected' : '';
}

$p = $perfil ?? [];
?>

<div class="transactions-container" style="align-items: flex-start;">
    
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success profile-alert">
            <i class="ph ph-check-circle" style="font-size: 18px;"></i> 
            Dados atualizados com sucesso!
        </div>
    <?php endif; ?>

    <!-- CARD 1: DADOS BÁSICOS DO USUÁRIO -->
    <div class="card form-container profile-card">
        <div class="card-header">
            <h4><i class="ph ph-user-gear" style="margin-right: 8px;"></i> <?= $titulo ?></h4>
        </div>

        <form action="/financas/perfil/update" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label>Nome Completo</label>
                <div class="input-with-icon">
                    <i class="ph ph-user"></i>
                    <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>E-mail de Acesso</label>
                <div class="input-with-icon">
                    <i class="ph ph-envelope-simple"></i>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                </div>
            </div>

            <hr class="form-divider">

            <div class="form-group">
                <label>Trocar Senha</label>
                <p class="text-secondary helper-text">Preencha apenas se quiser alterar a sua senha atual.</p>
                <div class="input-with-icon">
                    <i class="ph ph-lock-key"></i>
                    <input type="password" name="senha" class="form-control" placeholder="Digite a nova senha">
                </div>
            </div>

            <div class="form-actions" style="margin-top: 16px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-floppy-disk"></i> Atualizar Meus Dados
                </button>
            </div>
        </form>
    </div>

    <!-- CARD 2: PERFIL COMPORTAMENTAL (IA) -->
    <div class="card form-container profile-card mt-3">
        <div class="card-header">
            <h4><i class="ph ph-brain" style="margin-right: 8px;"></i> Perfil Comportamental (IA)</h4>
            <p class="helper-text">Atualize as informações para que o PREDITIV.IA ajuste seus conselhos.</p>
        </div>
    
        <div class="tabs-container">
            <div class="tabs-header">
                <button type="button" class="tab-btn active" data-target="tab-dividas">Dívidas & Desafios</button>
                <button type="button" class="tab-btn" data-target="tab-habitos">Hábitos & Gatilhos</button>
                <button type="button" class="tab-btn" data-target="tab-investimentos">Proteção</button>
                <button type="button" class="tab-btn" data-target="tab-renda">Renda Extra</button>
                <button type="button" class="tab-btn" data-target="tab-familia">Estrutura & Prazos</button>
            </div>

            <form action="/financas/perfil/atualizarIa" method="POST" class="mt-3">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- ABA 1: DÍVIDAS E DESAFIOS (6 perguntas) -->
                <div id="tab-dividas" class="tab-content active">
                    <div class="content-row">
                        <div class="form-group">
                            <label for="maior_problema">Qual é o seu maior desafio financeiro hoje?</label>
                            <select name="maior_problema" id="maior_problema" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Cartao de credito" <?= isSel('maior_problema', 'Cartao de credito', $p) ?>>Descontrole com cartão de crédito</option>
                                <option value="Pagar contas basicas" <?= isSel('maior_problema', 'Pagar contas basicas', $p) ?>>Dificuldade em pagar as contas básicas</option>
                                <option value="Falta de reserva" <?= isSel('maior_problema', 'Falta de reserva', $p) ?>>Não conseguir guardar dinheiro (reserva)</option>
                                <option value="Falta de investimentos" <?= isSel('maior_problema', 'Falta de investimentos', $p) ?>>Não saber como começar a investir</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="situacao_fim_mes">Como costuma ficar o seu saldo no fim do mês?</label>
                            <select name="situacao_fim_mes" id="situacao_fim_mes" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Sobra muito" <?= isSel('situacao_fim_mes', 'Sobra muito', $p) ?>>Sobra uma boa quantia</option>
                                <option value="Empatado" <?= isSel('situacao_fim_mes', 'Empatado', $p) ?>>Fica no zero a zero</option>
                                <option value="Falta" <?= isSel('situacao_fim_mes', 'Falta', $p) ?>>Falta dinheiro (entro no cheque especial)</option>
                            </select>
                        </div>
                    </div>
                    <div class="content-row">
                        <div class="form-group">
                            <label for="tem_dividas">Você possui dívidas em atraso ou empréstimos ativos?</label>
                            <select name="tem_dividas" id="tem_dividas" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Sim" <?= isSel('tem_dividas', 'Sim', $p) ?>>Sim, possuo</option>
                                <option value="Não" <?= isSel('tem_dividas', 'Não', $p) ?>>Não, estou livre de dívidas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipos_divida">Quais são os tipos das suas dívidas atuais?</label>
                            <select name="tipos_divida" id="tipos_divida" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Nao possuo" <?= isSel('tipos_divida', 'Nao possuo', $p) ?>>Não possuo dívidas</option>
                                <option value="Cartao e Cheque Especial" <?= isSel('tipos_divida', 'Cartao e Cheque Especial', $p) ?>>Cartão de Crédito / Cheque Especial</option>
                                <option value="Emprestimos Pessoais" <?= isSel('tipos_divida', 'Emprestimos Pessoais', $p) ?>>Empréstimos Pessoais / Consignado</option>
                                <option value="Financiamentos" <?= isSel('tipos_divida', 'Financiamentos', $p) ?>>Financiamento (Casa / Carro)</option>
                                <option value="Varias acumuladas" <?= isSel('tipos_divida', 'Varias acumuladas', $p) ?>>Várias acumuladas (Bola de neve)</option>
                            </select>
                        </div>
                    </div>
                    <div class="content-row">
                        <div class="form-group">
                            <label for="status_divida">Qual o status dessas dívidas?</label>
                            <select name="status_divida" id="status_divida" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Nao se aplica" <?= isSel('status_divida', 'Nao se aplica', $p) ?>>Não possuo dívidas</option>
                                <option value="Tudo em dia" <?= isSel('status_divida', 'Tudo em dia', $p) ?>>Tudo pago em dia</option>
                                <option value="Algumas em atraso" <?= isSel('status_divida', 'Algumas em atraso', $p) ?>>Algumas em atraso</option>
                                <option value="Totalmente inadimplente" <?= isSel('status_divida', 'Totalmente inadimplente', $p) ?>>Totalmente inadimplente (Nome sujo)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="valor_divida">Qual é o valor aproximado total das suas dívidas?</label>
                            <select name="valor_divida" id="valor_divida" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Nao possuo" <?= isSel('valor_divida', 'Nao possuo', $p) ?>>Não possuo</option>
                                <option value="Ate 1000" <?= isSel('valor_divida', 'Ate 1000', $p) ?>>Até R$ 1.000</option>
                                <option value="1001 a 5000" <?= isSel('valor_divida', '1001 a 5000', $p) ?>>De R$ 1.001 a R$ 5.000</option>
                                <option value="5001 a 15000" <?= isSel('valor_divida', '5001 a 15000', $p) ?>>De R$ 5.001 a R$ 15.000</option>
                                <option value="Acima de 15000" <?= isSel('valor_divida', 'Acima de 15000', $p) ?>>Acima de R$ 15.000</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ABA 2: HÁBITOS E GATILHOS (5 perguntas) -->
                <div id="tab-habitos" class="tab-content">
                    <div class="content-row">
                        <div class="form-group">
                            <label for="sentimento_dinheiro">Como você se sente em relação ao seu dinheiro hoje?</label>
                            <select name="sentimento_dinheiro" id="sentimento_dinheiro" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Ansioso" <?= isSel('sentimento_dinheiro', 'Ansioso', $p) ?>>Ansioso e preocupado</option>
                                <option value="Tranquilo" <?= isSel('sentimento_dinheiro', 'Tranquilo', $p) ?>>Tranquilo, mas quero melhorar</option>
                                <option value="No controle" <?= isSel('sentimento_dinheiro', 'No controle', $p) ?>>Totalmente no controle</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="controle_gastos">Como você acompanha seus gastos mensais?</label>
                            <select name="controle_gastos" id="controle_gastos" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Aplicativo" <?= isSel('controle_gastos', 'Aplicativo', $p) ?>>Aplicativo no celular</option>
                                <option value="Planilha" <?= isSel('controle_gastos', 'Planilha', $p) ?>>Planilha no computador</option>
                                <option value="Caderno" <?= isSel('controle_gastos', 'Caderno', $p) ?>>Caderno físico</option>
                                <option value="Cabeca" <?= isSel('controle_gastos', 'Cabeca', $p) ?>>Só de cabeça (não anoto)</option>
                            </select>
                        </div>
                    </div>
                    <div class="content-row">
                        <div class="form-group">
                            <label for="gatilho_gastos">O que mais costuma fazer você gastar por impulso?</label>
                            <select name="gatilho_gastos" id="gatilho_gastos" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Estresse e Ansiedade" <?= isSel('gatilho_gastos', 'Estresse e Ansiedade', $p) ?>>Estresse e Ansiedade ("Eu mereço")</option>
                                <option value="Promocoes" <?= isSel('gatilho_gastos', 'Promocoes', $p) ?>>Promoções imperdíveis na internet</option>
                                <option value="Eventos sociais" <?= isSel('gatilho_gastos', 'Eventos sociais', $p) ?>>Sair com amigos / Eventos sociais</option>
                                <option value="Comida" <?= isSel('gatilho_gastos', 'Comida', $p) ?>>Delivery e Restaurantes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tentou_organizar">Você já tentou se organizar financeiramente antes?</label>
                            <select name="tentou_organizar" id="tentou_organizar" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Sim e continuo" <?= isSel('tentou_organizar', 'Sim e continuo', $p) ?>>Sim, e continuo tentando</option>
                                <option value="Sim mas desisti" <?= isSel('tentou_organizar', 'Sim mas desisti', $p) ?>>Sim, mas acabei desistindo</option>
                                <option value="Nao nunca" <?= isSel('tentou_organizar', 'Nao nunca', $p) ?>>Não, é a primeira vez</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tentou_nao_funcionou">O que você já tentou fazer que não deu certo?</label>
                        <select name="tentou_nao_funcionou" id="tentou_nao_funcionou" class="form-control">
                            <option value="">Selecione...</option>
                            <option value="Nao se aplica" <?= isSel('tentou_nao_funcionou', 'Nao se aplica', $p) ?>>Nunca tentei nada antes</option>
                            <option value="Anotar tudo" <?= isSel('tentou_nao_funcionou', 'Anotar tudo', $p) ?>>Anotar todos os centavos (muito chato)</option>
                            <option value="Cortar tudo" <?= isSel('tentou_nao_funcionou', 'Cortar tudo', $p) ?>>Cortar tudo que gosto (muito frustrante)</option>
                            <option value="Planilhas complexas" <?= isSel('tentou_nao_funcionou', 'Planilhas complexas', $p) ?>>Usar planilhas muito complexas</option>
                        </select>
                    </div>
                </div>

                <!-- ABA 3: PROTEÇÃO E INVESTIMENTOS (7 perguntas) -->
                <div id="tab-investimentos" class="tab-content">
                    <div class="content-row">
                        <div class="form-group">
                            <label for="conhecimento_financeiro">Qual seu nível de conhecimento financeiro?</label>
                            <select name="conhecimento_financeiro" id="conhecimento_financeiro" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Iniciante" <?= isSel('conhecimento_financeiro', 'Iniciante', $p) ?>>Iniciante (sei muito pouco)</option>
                                <option value="Intermediario" <?= isSel('conhecimento_financeiro', 'Intermediario', $p) ?>>Intermediário (sei o básico)</option>
                                <option value="Avancado" <?= isSel('conhecimento_financeiro', 'Avancado', $p) ?>>Avançado (invisto e controlo bem)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="reserva_emergencia">Você possui alguma reserva para imprevistos?</label>
                            <select name="reserva_emergencia" id="reserva_emergencia" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Sim completa" <?= isSel('reserva_emergencia', 'Sim completa', $p) ?>>Sim, uma reserva segura</option>
                                <option value="Estou construindo" <?= isSel('reserva_emergencia', 'Estou construindo', $p) ?>>Estou construindo aos poucos</option>
                                <option value="Nao possuo" <?= isSel('reserva_emergencia', 'Nao possuo', $p) ?>>Não possuo nenhuma reserva</option>
                            </select>
                        </div>
                    </div>
                    <div class="content-row">
                        <div class="form-group">
                            <label for="meses_reserva">Quantos meses do seu custo de vida essa reserva cobre?</label>
                            <select name="meses_reserva" id="meses_reserva" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Nenhum" <?= isSel('meses_reserva', 'Nenhum', $p) ?>>Nenhum (Não possuo)</option>
                                <option value="Menos de 1 mes" <?= isSel('meses_reserva', 'Menos de 1 mes', $p) ?>>Menos de 1 mês</option>
                                <option value="1 a 3 meses" <?= isSel('meses_reserva', '1 a 3 meses', $p) ?>>De 1 a 3 meses</option>
                                <option value="Acima de 6 meses" <?= isSel('meses_reserva', 'Acima de 6 meses', $p) ?>>6 meses ou mais</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="local_reserva">Onde sua reserva está guardada?</label>
                            <select name="local_reserva" id="local_reserva" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Nao possuo" <?= isSel('local_reserva', 'Nao possuo', $p) ?>>Não possuo reserva</option>
                                <option value="Conta corrente" <?= isSel('local_reserva', 'Conta corrente', $p) ?>>Conta Corrente / Em casa</option>
                                <option value="Poupanca" <?= isSel('local_reserva', 'Poupanca', $p) ?>>Poupança</option>
                                <option value="Renda Fixa" <?= isSel('local_reserva', 'Renda Fixa', $p) ?>>CDB / Tesouro Direto / Contas Rendeiras</option>
                            </select>
                        </div>
                    </div>
                    <div class="content-row">
                        <div class="form-group">
                            <label for="ja_investiu">Além da reserva, você possui outros investimentos?</label>
                            <select name="ja_investiu" id="ja_investiu" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Sim frequentemente" <?= isSel('ja_investiu', 'Sim frequentemente', $p) ?>>Sim, invisto com frequência</option>
                                <option value="Sim mas parei" <?= isSel('ja_investiu', 'Sim mas parei', $p) ?>>Sim, mas parei</option>
                                <option value="Nunca investi" <?= isSel('ja_investiu', 'Nunca investi', $p) ?>>Nunca investi</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipos_investimento">Quais produtos financeiros você utiliza?</label>
                            <select name="tipos_investimento" id="tipos_investimento" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Nenhum" <?= isSel('tipos_investimento', 'Nenhum', $p) ?>>Nenhum no momento</option>
                                <option value="Apenas Poupanca" <?= isSel('tipos_investimento', 'Apenas Poupanca', $p) ?>>Apenas Poupança</option>
                                <option value="Renda Fixa" <?= isSel('tipos_investimento', 'Renda Fixa', $p) ?>>Renda Fixa (CDB, LCI, Tesouro)</option>
                                <option value="Renda Variavel" <?= isSel('tipos_investimento', 'Renda Variavel', $p) ?>>Renda Variável (Ações, FIIs, Cripto)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="conhece_conceitos">Você entende conceitos básicos como CDI, Selic e Inflação?</label>
                        <select name="conhece_conceitos" id="conhece_conceitos" class="form-control">
                            <option value="">Selecione...</option>
                            <option value="Sim entendo" <?= isSel('conhece_conceitos', 'Sim entendo', $p) ?>>Sim, entendo bem</option>
                            <option value="Mais ou menos" <?= isSel('conhece_conceitos', 'Mais ou menos', $p) ?>>Mais ou menos (já ouvi falar)</option>
                            <option value="Nao entendo" <?= isSel('conhece_conceitos', 'Nao entendo', $p) ?>>Não, é grego para mim</option>
                        </select>
                    </div>
                </div>

                <!-- ABA 4: RENDA EXTRA E REALIDADE (7 perguntas) -->
                <div id="tab-renda" class="tab-content">
                    <div class="content-row">
                        <div class="form-group">
                            <label for="renda_exata">Qual a sua renda mensal líquida exata?</label>
                            <div class="input-with-icon" style="position: relative;">
                                <span style="position: absolute; left: 16px; top: 14px; color: var(--text-secondary); font-weight: 500;">R$</span>
                                <input type="number" step="0.01" name="renda_exata" id="renda_exata" class="form-control" placeholder="0,00" style="padding-left: 45px;" value="<?= htmlspecialchars($p['renda_exata'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_renda">Qual é a sua principal fonte de renda?</label>
                            <select name="tipo_renda" id="tipo_renda" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="CLT" <?= isSel('tipo_renda', 'CLT', $p) ?>>CLT (Carteira Assinada)</option>
                                <option value="Autonomo/PJ" <?= isSel('tipo_renda', 'Autonomo/PJ', $p) ?>>Autônomo ou PJ</option>
                                <option value="Servidor Publico" <?= isSel('tipo_renda', 'Servidor Publico', $p) ?>>Servidor Público</option>
                                <option value="Desempregado/Estudante" <?= isSel('tipo_renda', 'Desempregado/Estudante', $p) ?>>Desempregado / Estudante</option>
                            </select>
                        </div>
                    </div>
                    <div class="content-row">
                        <div class="form-group">
                            <label for="quer_renda_extra">Você tem interesse em fazer renda extra?</label>
                            <select name="quer_renda_extra" id="quer_renda_extra" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Sim com urgencia" <?= isSel('quer_renda_extra', 'Sim com urgencia', $p) ?>>Sim, com muita urgência</option>
                                <option value="Sim se der tempo" <?= isSel('quer_renda_extra', 'Sim se der tempo', $p) ?>>Sim, se eu tiver tempo livre</option>
                                <option value="Nao tenho interesse" <?= isSel('quer_renda_extra', 'Nao tenho interesse', $p) ?>>Não tenho interesse agora</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pode_aumentar_renda">Existe viabilidade de aumento na sua renda principal?</label>
                            <select name="pode_aumentar_renda" id="pode_aumentar_renda" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Sim promocao" <?= isSel('pode_aumentar_renda', 'Sim promocao', $p) ?>>Sim (expectativa de promoção/novo emprego)</option>
                                <option value="Sim freelas" <?= isSel('pode_aumentar_renda', 'Sim freelas', $p) ?>>Sim (através de trabalhos extras/bicos)</option>
                                <option value="Muito dificil" <?= isSel('pode_aumentar_renda', 'Muito dificil', $p) ?>>Muito difícil no momento</option>
                            </select>
                        </div>
                    </div>
                    <div class="content-row">
                        <div class="form-group">
                            <label for="habilidades">Quais habilidades você possui que poderiam gerar renda?</label>
                            <select name="habilidades" id="habilidades" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Vendas e Comercio" <?= isSel('habilidades', 'Vendas e Comercio', $p) ?>>Vendas / Comércio</option>
                                <option value="Tecnologia" <?= isSel('habilidades', 'Tecnologia', $p) ?>>Tecnologia / Design / Edição</option>
                                <option value="Culinaria" <?= isSel('habilidades', 'Culinaria', $p) ?>>Culinária / Artesanato</option>
                                <option value="Ensino" <?= isSel('habilidades', 'Ensino', $p) ?>>Ensino / Aulas particulares / Consultoria</option>
                                <option value="Nenhuma especifica" <?= isSel('habilidades', 'Nenhuma especifica', $p) ?>>Nenhuma específica / Não sei</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="horas_disponiveis">Quantas horas por semana teria para um projeto extra?</label>
                            <select name="horas_disponiveis" id="horas_disponiveis" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Nenhuma" <?= isSel('horas_disponiveis', 'Nenhuma', $p) ?>>Nenhuma hora livre</option>
                                <option value="1 a 5 horas" <?= isSel('horas_disponiveis', '1 a 5 horas', $p) ?>>De 1 a 5 horas por semana</option>
                                <option value="6 a 10 horas" <?= isSel('horas_disponiveis', '6 a 10 horas', $p) ?>>De 6 a 10 horas por semana</option>
                                <option value="Mais de 10 horas" <?= isSel('horas_disponiveis', 'Mais de 10 horas', $p) ?>>Mais de 10 horas por semana</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="acesso_tecnologia">Quais equipamentos você tem acesso para trabalhar?</label>
                        <select name="acesso_tecnologia" id="acesso_tecnologia" class="form-control">
                            <option value="">Selecione...</option>
                            <option value="Computador e Internet" <?= isSel('acesso_tecnologia', 'Computador e Internet', $p) ?>>Computador e internet de qualidade</option>
                            <option value="Apenas Celular" <?= isSel('acesso_tecnologia', 'Apenas Celular', $p) ?>>Apenas o celular (smartphone)</option>
                            <option value="Acesso limitado" <?= isSel('acesso_tecnologia', 'Acesso limitado', $p) ?>>Acesso limitado a internet e equipamentos</option>
                        </select>
                    </div>
                </div>

                <!-- ABA 5: ESTRUTURA E PRAZOS (3 perguntas) -->
                <div id="tab-familia" class="tab-content">
                    <div class="content-row">
                        <div class="form-group">
                            <label for="objetivo_principal">Qual é o seu principal objetivo financeiro hoje?</label>
                            <select name="objetivo_principal" id="objetivo_principal" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Sair das dividas" <?= isSel('objetivo_principal', 'Sair das dividas', $p) ?>>Quitar minhas dívidas</option>
                                <option value="Reserva de emergencia" <?= isSel('objetivo_principal', 'Reserva de emergencia', $p) ?>>Criar uma reserva de emergência</option>
                                <option value="Comecar a investir" <?= isSel('objetivo_principal', 'Comecar a investir', $p) ?>>Aprender a investir</option>
                                <option value="Aumentar patrimonio" <?= isSel('objetivo_principal', 'Aumentar patrimonio', $p) ?>>Aumentar meu patrimônio</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tempo_melhoria">Em quanto tempo espera ver melhorias nas finanças?</label>
                            <select name="tempo_melhoria" id="tempo_melhoria" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Imediato" <?= isSel('tempo_melhoria', 'Imediato', $p) ?>>Imediatamente (estou no vermelho)</option>
                                <option value="Curto prazo" <?= isSel('tempo_melhoria', 'Curto prazo', $p) ?>>Curto prazo (até 6 meses)</option>
                                <option value="Medio prazo" <?= isSel('tempo_melhoria', 'Medio prazo', $p) ?>>Médio prazo (1 a 2 anos)</option>
                                <option value="Longo prazo" <?= isSel('tempo_melhoria', 'Longo prazo', $p) ?>>Longo prazo (mais de 2 anos, sem pressa)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dependentes">Quantas pessoas dependem financeiramente de você?</label>
                        <select name="dependentes" id="dependentes" class="form-control">
                            <option value="">Selecione...</option>
                            <option value="Nenhum" <?= isSel('dependentes', 'Nenhum', $p) ?>>Somente eu (0 dependentes)</option>
                            <option value="1 pessoa" <?= isSel('dependentes', '1 pessoa', $p) ?>>1 pessoa</option>
                            <option value="2 a 3 pessoas" <?= isSel('dependentes', '2 a 3 pessoas', $p) ?>>De 2 a 3 pessoas</option>
                            <option value="4 ou mais" <?= isSel('dependentes', '4 ou mais', $p) ?>>4 ou mais pessoas</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions mt-3" style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-primary">Atualizar IA <i class="ph ph-brain"></i></button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                const targetId = btn.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');
            });
        });
    });
    </script>
</div>