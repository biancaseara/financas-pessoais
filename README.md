# 💰 PREDITIV.IA - Sistema de Gestão Financeira Pessoal

Um sistema web completo para controle de finanças pessoais, construído em PHP com arquitetura MVC (Model-View-Controller) pura, sem o uso de frameworks. O objetivo do projeto é oferecer um controle rigoroso de receitas, despesas fixas, limites de gastos e acompanhamento de carteira, com foco em segurança e usabilidade.

## 🚀 Funcionalidades

- **Dashboard Interativo:** Resumo financeiro mensal e gráficos dinâmicos de despesas por categoria usando Chart.js.
- **Autenticação e Conformidade (LGPD):** Sistema de login seguro com hash de senhas (`password_hash`), registro de usuários e validação obrigatória de Termos de Uso e Política de Privacidade.
- **Gestão de Transações:** Registro de Entradas, Saídas e Transferências entre contas com cálculo automático de estornos em caso de edição/exclusão.
- **Orçamento (Budget):** Definição de limites mensais de gastos por categoria, com barras de progresso visuais no painel.
- **Despesas Fixas (Automação):** Cadastro de assinaturas mensais e contas fixas, com um "robô" (script) de lançamento em lote para o mês atual.
- **Módulo de Investimentos:** Acompanhamento de carteira (CDB, Tesouro Direto, etc.) com atualização manual de rendimentos.
- **Controle de Acesso (ACL):** Sistema de perfis com permissões de Administrador (gestão de usuários) e Usuário Comum (acesso apenas ao próprio perfil e finanças).
- **UX/UI Aprimorada:** Interface responsiva com **Modo Escuro (Dark Mode) automático** baseado na preferência do sistema operacional (`prefers-color-scheme`). 

## 🛠️ Tecnologias Utilizadas

- **Back-end:** PHP 8+ (Vanilla / Orientado a Objetos)
- **Banco de Dados:** MySQL (PDO)
- **Front-end:** HTML5, CSS3, JavaScript (Vanilla e jQuery)
- **Bibliotecas:** Chart.js (Gráficos)
- **Arquitetura:** MVC (Model-View-Controller) com roteamento amigável via `.htaccess`.

## 🗄️ Estrutura do Banco de Dados

O banco de dados (`financas_pessoais`) é totalmente relacional, garantindo a integridade dos dados através de chaves estrangeiras (`FOREIGN KEY`) e deleção em cascata (`ON DELETE CASCADE`).

### Tabelas Principais:

1. **`usuarios`**
   - `id_usuario` (PK)
   - `nome`, `email`, `senha` (Hash)
   - `aceitou_termos` (TINYINT - Validação de aceite de Termos de Uso)
   - `data_aceite_termos` (DATETIME - Registro de conformidade LGPD)
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

- [ ] **Integração de IA (Preditivo):** Implementar análises preditivas para alertar o usuário sobre possíveis estouros de orçamento com base no histórico de gastos.
- [ ] **Autoloading com Composer (PSR-4):** Substituir as chamadas manuais de `require_once` pelo padrão PSR-4 utilizando o Composer, otimizando o carregamento de classes.
- [ ] **Camada de Helpers:** Criar o diretório `app/Helpers/` para centralizar funções utilitárias globais (ex: formatação de moeda BRL, manipulação e conversão de datas), aplicando o princípio DRY.

## ⚙️ Como Executar Localmente

### Pré-requisitos
- Servidor Web (Apache/Nginx) ou PHP CLI
- MySQL Server
- Módulo `mod_rewrite` habilitado (caso use Apache)

### Passos de Instalação

1. Clone o repositório:
   ```bash
   git clone [https://github.com/seu-usuario/financas-pessoais.git](https://github.com/seu-usuario/financas-pessoais.git)

2. Importe o banco de dados (crie as tabelas conforme a estrutura acima no MySQL).

3. Configure as credenciais do banco no arquivo config/database.php.

### Rodando o Projeto
- Opção A: Usando o Servidor Embutido do PHP (Recomendado para Desenvolvimento Rápido)
*Abra o terminal na pasta raiz do projeto e execute:*
   ```bash
   php -S localhost:8000 [http://localhost:8000](http://localhost:8000)

- Opção B: Usando Apache (Linux/Ubuntu)
   1. Mova a pasta do projeto para /var/www/html/financas.

   2. Certifique-se de que o .htaccess está presente na pasta raiz.

   3. Acesse http://localhost/financas no seu navegador.
