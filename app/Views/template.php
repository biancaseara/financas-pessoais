<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PREDITIV.IA</title>
    <link rel="stylesheet" href="/financas/public/css/style.css">

    <link rel="icon" type="image/png" sizes="192x192" href="/financas/public/images/icon-fill-192.png">
    <link rel="manifest" href="/financas/public/manifest.json">
    <link rel="apple-touch-icon" href="/financas/public/images/icon-fill-192.png">
    <meta name="theme-color" content="#000000">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
    <?php if (isset($_SESSION['id_usuario'])): ?>
        <div class="nav-bar">
            <a href="/financas">🏠 Dashboard</a>
            <a href="/financas/transacoes">💸 Transações</a>
            <a href="/financas/recorrentes">🔄 Despesas Fixas</a>
            <a href="/financas/contas">🏦 Contas</a>

            <a href="/financas/cartoes">💳 Cartões</a>

            <a href="/financas/categorias">📂 Categorias</a>
            <a href="/financas/metas">🎯 Metas</a>
            <a href="/financas/investimentos">📈 Investimentos</a>

            <?php if (isset($_SESSION['perfil']) && $_SESSION['perfil'] == 'admin'): ?>
                <a href="/financas/usuarios">👤 Usuários</a>
            <?php endif; ?>

            <a href="/financas/perfil">⚙️ Meu Perfil</a>
            <a href="/financas/auth/logout">🚪 Sair</a>
        </div>
    <?php endif; ?>

    <div class="container">
        <?php require_once $arquivoView; ?>
    </div>

</body>

</html>