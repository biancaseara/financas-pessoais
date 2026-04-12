# Engenharia de Requisitos

## 1. Requisitos Funcionais (RF)
* **RF01 - Autenticação e Perfil:** O sistema deve permitir o cadastro de novos usuários, login, logout e edição dos próprios dados cadastrais.
* **RF02 - Gestão de Contas Bancárias:** O usuário deve poder cadastrar, editar, excluir e visualizar contas, definindo saldo inicial e cor.
* **RF03 - Controle de Categorias:** O sistema deve permitir criar categorias de receitas e despesas com definição de limite mensal.
* **RF04 - Gestão de Transações:** O usuário deve conseguir lançar receitas, despesas e transferências entre contas.
* **RF05 - Automação de Despesas Recorrentes:** O sistema deve permitir o cadastro de contas fixas e possuir um gatilho para lançá-las automaticamente no mês.
* **RF06 - Controle de Metas:** O sistema deve permitir o registro e acompanhamento de objetivos financeiros.
* **RF07 - Gestão de Investimentos:** O usuário deve poder registrar aplicações informando corretora e vencimento.
* **RF08 - Dashboard e Relatórios:** O sistema deve apresentar saldos, transações recentes, status dos orçamentos e gráficos.
* **RF09 - Gestão Administrativa:** O sistema deve possuir perfil "Admin" para gerenciar todos os usuários da plataforma.

## 2. Requisitos Não Funcionais (RNF)
* **RNF01 - Arquitetura:** O sistema deve ser desenvolvido em PHP utilizando o padrão MVC.
* **RNF02 - Segurança de Dados:** Senhas devem ser criptografadas utilizando algoritmos de hash nativos (BCRYPT).
* **RNF03 - Controle de Acesso:** Rotas administrativas devem ser bloqueadas para usuários de perfil comum.
* **RNF04 - Usabilidade:** A interface deve fornecer feedback visual (mensagens de erro/sucesso) após submissões.

## 3. Principais Casos de Uso
* **UC01 - Lançar Transação:** Usuário comum acessa transações, seleciona tipo, conta, categoria e valor. O sistema debita/credita e atualiza o Dashboard.
* **UC02 - Processar Mês Recorrente:** Usuário aciona o lançamento do mês. O sistema lê contas ativas e gera transações com a tag "🔄".
* **UC03 - Gestão de Acesso:** Administrador altera permissões ou exclui registros através do painel de usuários.