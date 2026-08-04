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

    <!-- Sistema de Feedback Visual (Flash Messages) - Mensagens de Sucesso, Erro, Aviso e Informação -->
    <?php if (isset($flash) && $flash): ?>
        <div id="flash-toast" class="toast-notification toast-<?= htmlspecialchars($flash['tipo'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="toast-icon">
                <?php if ($flash['tipo'] == 'success'): ?>
                    <i class="ph ph-check-circle"></i>
                <?php elseif ($flash['tipo'] == 'error'): ?>
                    <i class="ph ph-warning-circle"></i>
                <?php elseif ($flash['tipo'] == 'warning'): ?>
                    <i class="ph ph-warning"></i>
                <?php else: ?>
                    <i class="ph ph-info"></i>
                <?php endif; ?>
            </div>
            <div class="toast-message"><?= htmlspecialchars($flash['mensagem'], ENT_QUOTES, 'UTF-8') ?></div>
            <button class="toast-close" onclick="document.getElementById('flash-toast').remove()">
                <i class="ph ph-x"></i>
            </button>
        </div>

        <style>
            .toast-notification {
                position: fixed;
                top: 24px;
                right: 24px;
                z-index: 9999;
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 16px 20px;
                border-radius: 8px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                color: #fff;
                animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
                min-width: 250px;
                max-width: 400px;
            }
            .toast-success { background-color: var(--color-emerald, #10b981); }
            .toast-error { background-color: var(--color-rose, #f43f5e); }
            .toast-warning { background-color: #f59e0b; color: #fff; }
            .toast-info { background-color: var(--color-ia-purple, #8b5cf6); }
            .toast-icon { font-size: 1.5rem; display: flex; }
            .toast-message { flex-grow: 1; font-weight: 500; font-size: 0.95rem; line-height: 1.4; }
            .toast-close { background: none; border: none; color: white; cursor: pointer; opacity: 0.7; font-size: 1.2rem; display: flex; transition: opacity 0.2s; }
            .toast-close:hover { opacity: 1; }
            
            @keyframes slideInRight {
                from { transform: translateX(120%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(120%); opacity: 0; }
            }
            .toast-fade-out {
                animation: slideOutRight 0.4s ease forwards;
            }
        </style>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('flash-toast');
                if (toast) {
                    toast.classList.add('toast-fade-out');
                    setTimeout(() => toast.remove(), 400);
                }
            }, 4000);
        </script>
    <?php endif; ?>
</body>

</html>