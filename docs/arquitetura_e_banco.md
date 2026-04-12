# Projeto e Modelagem

## 1. Arquitetura do Software
O projeto adota o padrão **MVC (Model-View-Controller)** puro (Vanilla PHP), garantindo a separação entre a lógica de negócios, a interface do usuário e o controle de fluxo. 
Para a camada de persistência, utiliza-se **PDO (PHP Data Objects)**. 
* **Regra de Integridade (Atomicidade):** Para garantir que não haja inconsistências financeiras (ex: dinheiro sair de uma conta e não entrar na outra durante uma transferência), o sistema utiliza transações nativas do banco (`beginTransaction`, `commit` e `rollBack`).

## 2. Modelo Lógico de Banco de Dados
O sistema é relacional, mapeando as seguintes entidades principais:
* **Usuarios:** `id_usuario` (PK), nome, email, senha, perfil.
* **Contas:** `id_conta` (PK), `id_usuario` (FK), nome_banco, saldo_inicial.
* **Categorias:** `id_categoria` (PK), `id_usuario` (FK), nome_categoria, tipo, limite_mensal.
* **Transacoes:** `id_transacao` (PK), `id_conta` (FK), `id_categoria` (FK), `id_conta_destino` (FK), valor, data_transacao, tipo_transacao.
* **Despesas Recorrentes:** `id_recorrente` (PK), `id_usuario` (FK), `id_conta` (FK), `id_categoria` (FK), valor, dia_vencimento.
* **Metas e Investimentos:** Entidades atreladas ao `id_usuario` (FK) para acompanhamento de patrimônio.