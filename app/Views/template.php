<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">

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
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>

    <?php if (isset($_SESSION['id_usuario'])): ?>
        <div id="sidebar-overlay" class="overlay"></div>

        <aside class="sidebar">
            <div class="logo-container">
                <div class="favicon-ia">
                    <span class="dot"></span>IA
                </div>
                <h1 class="logo-text">PREDITIV<span class="highlight">.IA</span></h1>
            </div>

            <nav class="menu">
                <a href="/financas" class="menu-item"><i class="ph ph-squares-four"></i> Dashboard</a>
                <a href="/financas/transacoes" class="menu-item"><i class="ph ph-arrows-left-right"></i> Transações</a>
                <a href="/financas/recorrentes" class="menu-item"><i class="ph ph-calendar-check"></i> Despesas Fixas</a>
                <a href="/financas/contas" class="menu-item"><i class="ph ph-bank"></i> Contas</a>
                <a href="/financas/cartoes" class="menu-item"><i class="ph ph-credit-card"></i> Cartões</a>
                <a href="/financas/categorias" class="menu-item"><i class="ph ph-list-dashes"></i> Categorias</a>
                <a href="/financas/metas" class="menu-item"><i class="ph ph-target"></i> Metas</a>
                <a href="/financas/investimentos" class="menu-item"><i class="ph ph-trend-up"></i> Investimentos</a>

                <?php if (isset($_SESSION['perfil']) && $_SESSION['perfil'] == 'admin'): ?>
                    <a href="/financas/usuarios" class="menu-item"><i class="ph ph-users"></i> Painel de Admin</a>
                <?php endif; ?>
            </nav>

            <div class="user-footer">
                <div class="user-info">
                    <div class="avatar">
                        <?php
                        $nomeUsuario = $_SESSION['nome'] ?? 'Usuário';
                        echo strtoupper(substr($nomeUsuario, 0, 1));
                        ?>
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($nomeUsuario); ?></span>
                        <a href="/financas/perfil" class="profile-link">Meu Perfil</a>
                    </div>
                </div>
                <a href="/financas/auth/logout" class="logout-btn" title="Sair"><i class="ph ph-sign-out"></i></a>
            </div>
        </aside>
    <?php endif; ?>

    <main class="main-content">

        <?php if (isset($_SESSION['id_usuario'])): ?>
            <header class="topbar">
                <div class="topbar-left">
                    <button id="mobile-menu-btn" class="icon-btn mobile-only"><i class="ph ph-list"></i></button>
                    <div class="greeting">
                        <h2>Olá, <?php echo htmlspecialchars(explode(' ', $_SESSION['nome'] ?? 'Usuário')[0]); ?>! 👋</h2>
                        <p class="text-secondary">Aqui está o resumo das suas finanças.</p>
                    </div>
                </div>

                <div class="topbar-actions">
                    <button id="theme-toggle" class="icon-btn" title="Alternar Tema"><i class="ph ph-moon"></i></button>
                    <button class="btn-outline header-btn"><i class="ph ph-calendar-plus"></i> Despesa Fixa</button>
                    <button class="btn-primary"><i class="ph ph-plus"></i> Novo Lançamento</button>
                </div>
            </header>
        <?php endif; ?>

        <div class="conteudo-da-pagina">
            <?php require_once $arquivoView; ?>
        </div>

    </main>

    <?php if (isset($_SESSION['id_usuario'])): ?>
        <script>
            // Tema Light/Dark
            const themeToggle = document.getElementById('theme-toggle');
            const htmlElement = document.documentElement;
            const icon = themeToggle.querySelector('i');

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    if (htmlElement.getAttribute('data-theme') === 'dark') {
                        htmlElement.setAttribute('data-theme', 'light');
                        icon.classList.replace('ph-moon', 'ph-sun');
                    } else {
                        htmlElement.setAttribute('data-theme', 'dark');
                        icon.classList.replace('ph-sun', 'ph-moon');
                    }
                });
            }

            // Script do Menu Mobile
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleMenu() {
                if (sidebar) sidebar.classList.toggle('open');
                if (overlay) overlay.classList.toggle('active');
            }

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', toggleMenu);
            }

            if (overlay) {
                overlay.addEventListener('click', toggleMenu);
            }
        </script>
    <?php endif; ?>

</body>

</html>