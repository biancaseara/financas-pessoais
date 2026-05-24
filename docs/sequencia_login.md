<!-- ctrl + shift + v -->
# Diagrama de Sequência - Autenticação

```mermaid
sequenceDiagram
    autonumber
    actor U as Usuário
    participant V as View (Login)
    participant C as AuthController
    participant M as Usuario (Model)
    participant BD as Banco de Dados

    U->>V: Insere E-mail e Senha
    V->>C: Envia dados (POST)
    C->>M: Busca usuário por e-mail
    M->>BD: SELECT * FROM usuarios WHERE email = ?
    BD-->>M: Retorna os dados
    
    alt Usuário não encontrado ou senha errada
        M-->>C: Retorna falso/erro
        C-->>V: Redireciona com erro
        V-->>U: Exibe "Credenciais inválidas"
    else Senha correta
        M-->>C: Retorna dados validados
        C->>C: Cria sessão do usuário (Session)
        C-->>V: Redireciona para Dashboard
        V-->>U: Exibe a tela inicial do PREDITIV.IA
    end