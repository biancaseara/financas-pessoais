<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PREDITIV.IA</title>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">

    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo BASE_URL; ?>/images/icon-fill-192.png">
    <link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.json">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/images/icon-fill-192.png">
    <meta name="theme-color" content="#000000">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>

    <?php if (isset($_SESSION['id_usuario'])): ?>
        <div id="sidebar-overlay" class="overlay"></div>

        <aside class="sidebar">
            <div class="logo-container">
                <a href="<?php echo BASE_URL; ?>/financas" class="logo-link">
                    <img src="<?php echo BASE_URL; ?>/images/logo.png" alt="Ícone PREDITIV.IA" class="logo-img">
                    <h1 class="logo-text">PREDITIV<span class="highlight">.IA</span></h1>
                </a>
            </div>

            <?php
            // VERIFICA URL PARA SABER EM QUE PÁGINA ESTÁ E APLICAR A CLASSE ACTIVE NO MENU
            $urlAtual = $_SERVER['REQUEST_URI'];
            ?>

            <nav class="menu">
                <a href="/financas" class="menu-item <?= ($urlAtual == '/financas' || $urlAtual == '/financas/') ? 'active' : '' ?>">
                    <i class="ph ph-squares-four"></i> Dashboard
                </a>

                <a href="/financas/transacoes" class="menu-item <?= (strpos($urlAtual, '/financas/transacoes') !== false) ? 'active' : '' ?>">
                    <i class="ph ph-arrows-left-right"></i> Transações
                </a>

                <a href="/financas/recorrentes" class="menu-item <?= (strpos($urlAtual, '/financas/recorrentes') !== false) ? 'active' : '' ?>">
                    <i class="ph ph-calendar-check"></i> Despesas Fixas
                </a>

                <a href="/financas/contas" class="menu-item <?= (strpos($urlAtual, '/financas/contas') !== false) ? 'active' : '' ?>">
                    <i class="ph ph-bank"></i> Contas
                </a>

                <a href="/financas/cartoes" class="menu-item <?= (strpos($urlAtual, '/financas/cartoes') !== false) ? 'active' : '' ?>">
                    <i class="ph ph-credit-card"></i> Cartões
                </a>

                <a href="/financas/categorias" class="menu-item <?= (strpos($urlAtual, '/financas/categorias') !== false) ? 'active' : '' ?>">
                    <i class="ph ph-list-dashes"></i> Categorias
                </a>

                <a href="/financas/metas" class="menu-item <?= (strpos($urlAtual, '/financas/metas') !== false) ? 'active' : '' ?>">
                    <i class="ph ph-target"></i> Metas
                </a>

                <a href="/financas/investimentos" class="menu-item <?= (strpos($urlAtual, '/financas/investimentos') !== false) ? 'active' : '' ?>">
                    <i class="ph ph-trend-up"></i> Investimentos
                </a>

                <?php if (isset($_SESSION['perfil']) && in_array($_SESSION['perfil'], ['admin', 'super_admin'])): ?>
                    <a href="/financas/usuarios" class="menu-item <?= (strpos($urlAtual, '/financas/usuarios') !== false) ? 'active' : '' ?>">
                        <i class="ph ph-users"></i> Painel de Admin
                    </a>
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
                    <button id="theme-toggle" class="icon-btn" title="Alternar Tema"><i class="ph ph-sun"></i></button>

                    <form action="/financas/recorrentes/lancarMes" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn-outline header-btn" onclick="return confirm('Deseja lançar todas as despesas fixas ativas deste mês?');">
                            <i class="ph ph-calendar-plus"></i> Despesa Fixa
                        </button>
                    </form>

                    <a href="/financas/transacoes" class="btn-primary">
                        <i class="ph ph-plus"></i> Novo Lançamento
                    </a>
                </div>
            </header>
        <?php endif; ?>

        <div class="conteudo-da-pagina">
            <?php require_once $arquivoView; ?>
        </div>

    </main>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        let icon = null;

        if (themeToggle) {
            icon = themeToggle.querySelector('i');
        }

        function applyTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            localStorage.setItem('preditiv_theme', theme);

            if (icon) {
                if (theme === 'light') {
                    icon.classList.remove('ph-sun');
                    icon.classList.add('ph-moon');
                } else {
                    icon.classList.remove('ph-moon');
                    icon.classList.add('ph-sun');
                }
            }
        }

        // 2. Descobre a preferência do sistema operacional do usuário
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const defaultTheme = systemPrefersDark ? 'dark' : 'light';

        // 3. Aplica o tema salvo OU o tema do sistema
        const savedTheme = localStorage.getItem('preditiv_theme') || defaultTheme;
        applyTheme(savedTheme);

        // 4. Evento do botão de trocar tema (só funciona onde o botão existir)
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
            });
        }
    </script>

    <?php if (isset($_SESSION['id_usuario'])): ?>
        <script>
            // HAMBURGER MENU
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