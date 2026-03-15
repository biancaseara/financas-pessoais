# 💰 Sistema de Gestão Financeira Pessoal

Um sistema web completo para controle de finanças pessoais, construído em PHP com arquitetura MVC (Model-View-Controller) pura, sem o uso de frameworks. O objetivo do projeto é oferecer um controle rigoroso de receitas, despesas fixas, limites de gastos e carteira de investimentos.

## 🚀 Funcionalidades

- **Dashboard Interativo:** Resumo financeiro mensal e gráficos dinâmicos de despesas por categoria usando Chart.js.
- **Gestão de Transações:** Registro de Entradas, Saídas e Transferências entre contas com cálculo automático de estornos em caso de edição/exclusão.
- **Orçamento (Budget):** Definição de limites mensais de gastos por categoria, com barras de progresso visuais no painel.
- **Despesas Fixas (Automação):** Cadastro de assinaturas mensais e contas fixas, com um "robô" (script) de lançamento em lote para o mês atual.
- **Módulo de Investimentos:** Acompanhamento de carteira (CDB, Tesouro Direto, etc.) com atualização manual de rendimentos.
- **Controle de Acesso (ACL):** Sistema de perfis com permissões de Administrador (gestão de usuários) e Usuário Comum (acesso apenas ao próprio perfil e finanças).
- **UX/UI Aprimorada:** Interface responsiva com **Modo Escuro (Dark Mode) automático** baseado na preferência do sistema operacional (`prefers-color-scheme`). Suporte para instalação como PWA (Web App de Desktop).

## 🛠️ Tecnologias Utilizadas

- **Back-end:** PHP 8+ (Vanilla / Orientado a Objetos)
- **Banco de Dados:** MySQL
- **Front-end:** HTML5, CSS3, JavaScript (Vanilla e jQuery)
- **Bibliotecas:** Chart.js (Gráficos)
- **Arquitetura:** MVC (Model-View-Controller)

## 🗄️ Estrutura do Banco de Dados

O banco de dados (`financas_pessoais`) é totalmente relacional, garantindo a integridade dos dados através de chaves estrangeiras (`FOREIGN KEY`) e deleção em cascata (`ON DELETE CASCADE`).

### Tabelas Principais:

1. **`usuarios`**
   - `id_usuario` (PK)
   - `nome`, `email`, `senha` (Hash)
   - `perfil` (ENUM: 'admin', 'comum')
   - `data_cadastro`

2. **`contas`**
   - `id_conta` (PK)
   - `id_usuario` (FK)
   - `nome_banco`
   - `saldo` (Atualizado dinamicamente)

3. **`categorias`**
   - `id_categoria` (PK)
   - `id_usuario` (FK)
   - `nome_categoria`
   - `tipo` (ENUM: 'R' para Receita, 'D' para Despesa)
   - `limite_mensal` (NULLABLE)

4. **`transacoes`**
   - `id_transacao` (PK)
   - `id_conta` (FK)
   - `id_categoria` (FK - NULLABLE para transferências)
   - `id_conta_destino` (FK - NULLABLE, usada apenas em transferências)
   - `descricao`, `valor`, `data_transacao`
   - `tipo_transacao` (ENUM: 'Entrada', 'Saida', 'Transferencia')

5. **`despesas_recorrentes`**
   - `id_recorrente` (PK)
   - `id_usuario` (FK)
   - `id_conta` (FK), `id_categoria` (FK)
   - `descricao`, `valor`, `dia_vencimento`
   - `status` (ENUM: 'Ativo', 'Inativo')

6. **`investimentos`**
   - `id_investimento` (PK)
   - `id_usuario` (FK)
   - `nome_investimento`, `tipo`, `corretora`
   - `valor_aplicado`, `data_aplicacao`, `vencimento` (NULLABLE)

## 🗺️ Roadmap Futuro (Próximos Passos da Arquitetura)

Este projeto está em constante evolução. Os próximos passos focam em escalar a base de código para padrões corporativos:

- [ ] **Variáveis de Ambiente (`.env`):** Implementar a leitura de arquivos `.env` para isolar credenciais de banco de dados e chaves de segurança, removendo-as do código-fonte hardcoded (`config/database.php`).
- [ ] **Autoloading com Composer (PSR-4):** Substituir as chamadas manuais de `require_once` pelo padrão PSR-4 utilizando o Composer, otimizando o carregamento de classes de Controllers e Models.
- [ ] **Camada de Helpers:** Criar o diretório `app/Helpers/` para centralizar funções utilitárias globais (ex: formatação de moeda BRL, manipulação e conversão de datas), aplicando o princípio DRY (Don't Repeat Yourself).
- [ ] **Filtros e Paginação:** Adicionar paginação e filtros de busca (por mês/ano e categoria) na tela de listagem de Transações.

## ⚙️ Como Executar Localmente

1. Clone o repositório.
2. Importe o banco de dados (crie as tabelas conforme a estrutura acima no MySQL).
3. Configure as credenciais do banco no arquivo `app/config/database.php`.
4. Inicie o servidor embutido do PHP apontando para a pasta `public`:
   ```bash
   php -S localhost:8000 -t public
5. Acesse http://localhost:8000 no seu navegador.