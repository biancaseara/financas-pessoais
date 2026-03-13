<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Financeiro</title>
    <link rel="stylesheet" href="/financas/public/css/style.css">
</head>

<body>
    <?php if (isset($_SESSION['id_usuario'])): ?>
        <div class="nav-bar">
            <a href="/financas">🏠 Dashboard</a>
            <a href="/financas/transacoes">💸 Transações</a>
            <a href="/financas/contas">🏦 Contas</a>
            <a href="/financas/categorias">📂 Categorias</a>
            <a href="/financas/metas">🎯 Metas</a>

            <?php if (isset($_SESSION['perfil']) && $_SESSION['perfil'] == 'admin'): ?>
                <a href="/financas/usuarios">👤 Usuários</a>
            <?php endif; ?>

            <a href="/financas/auth/logout">🚪 Sair</a>
        </div>
    <?php endif; ?>

    <div class="container">
        <?php require_once $arquivoView; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>

</html>