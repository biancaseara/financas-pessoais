```mermaid
flowchart LR
    %% Atores
    U((Usuário))
    T((Bot do Telegram))

    %% Casos de Uso principais
    C1([Visualizar Dashboard Financeiro])
    C2([Gerenciar Transações e Cartões])
    C3([Consultar Previsões da IA])
    C4([Registrar Despesa via Chat])
    C5([Gerenciar Metas e Recorrências])

    %% Relacionamentos do Usuário
    U --> C1
    U --> C2
    U --> C3
    U --> C5

    %% Relacionamentos do Bot
    T --> C4
    
    %% O registro via chat alimenta o gerenciamento de transações
    C4 -.->|Alimenta| C2