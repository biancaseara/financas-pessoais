<div class="transactions-container">
    
    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
        <i class="ph-fill ph-crown" style="font-size: 32px; color: var(--color-ia-purple);"></i>
        <h2 style="margin: 0; color: var(--text-primary);"> <?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>
    </div>

    <!-- MÓDULO DE SAÚDE DA IA (Visível apenas para o Super Admin) -->
    <?php if ($_SESSION['perfil'] === 'super_admin'): ?>
        
        <!-- Cards de KPI -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon purple"><i class="ph-fill ph-brain"></i></div>
                <div class="kpi-data">
                    <span class="kpi-label">Requisições à IA</span>
                    <h3 class="kpi-value"><?= number_format($metricasIa['total_requisicoes'] ?? 0, 0, ',', '.') ?></h3>
                </div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-icon blue"><i class="ph-fill ph-coins"></i></div>
                <div class="kpi-data">
                    <span class="kpi-label">Tokens Consumidos</span>
                    <h3 class="kpi-value"><?= number_format($metricasIa['total_tokens'] ?? 0, 0, ',', '.') ?></h3>
                </div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-icon emerald"><i class="ph-fill ph-clock-countdown"></i></div>
                <div class="kpi-data">
                    <span class="kpi-label">Latência Média</span>
                    <h3 class="kpi-value"><?= number_format($metricasIa['latencia_media'] ?? 0, 0, ',', '.') ?> ms</h3>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon danger"><i class="ph-fill ph-warning-circle"></i></div>
                <div class="kpi-data">
                    <span class="kpi-label">Erros de API</span>
                    <h3 class="kpi-value"><?= number_format($metricasIa['total_erros'] ?? 0, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>

        <!-- Gráfico de Uso da IA -->
        <div class="card mb-4">
            <div class="card-header">
                <h4><i class="ph ph-chart-line-up" style="margin-right: 8px;"></i> Uso do Gemini (Últimos 7 dias)</h4>
            </div>
            <div style="padding: 20px; height: 300px; width: 100%;">
                <canvas id="graficoIa"></canvas>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const dadosGrafico = <?= json_encode($graficoIa ?? []) ?>;
                
                const labels = dadosGrafico.map(d => {
                    const data = new Date(d.data_dia + 'T00:00:00');
                    return data.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                });
                const dadosRequisicoes = dadosGrafico.map(d => d.total_requisicoes);
                
                const ctx = document.getElementById('graficoIa').getContext('2d');
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Requisições',
                            data: dadosRequisicoes,
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#8b5cf6',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, color: isDark ? '#9ca3af' : '#6b7280' },
                                grid: { color: isDark ? '#1f2937' : '#e5e7eb' }
                            },
                            x: {
                                ticks: { color: isDark ? '#9ca3af' : '#6b7280' },
                                grid: { display: false }
                            }
                        }
                    }
                });
            });
        </script>
    <?php endif; ?>

    <!-- MÓDULO DE GESTÃO DE USUÁRIOS -->
    <div class="card form-container mb-4">
        <div class="card-header">
            <h4><i class="ph ph-user-plus" style="margin-right: 8px;"></i> Cadastrar Novo Usuário</h4>
        </div>

        <form action="/financas/usuarios/store" method="POST" class="transaction-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <div class="input-with-icon">
                        <i class="ph ph-user"></i>
                        <input type="text" name="nome" class="form-control" placeholder="Nome do usuário" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>E-mail (Receberá o link de senha)</label>
                    <div class="input-with-icon">
                        <i class="ph ph-envelope-simple"></i>
                        <input type="email" name="email" class="form-control" placeholder="exemplo@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Perfil de Acesso</label>
                    <div class="input-with-icon">
                        <i class="ph ph-shield-check"></i>
                        <select name="perfil" class="form-control" required>
                            <option value="comum">Usuário Comum</option>
                            <option value="admin">Administrador</option>
                            <?php if ($_SESSION['perfil'] === 'super_admin'): ?>
                                <option value="super_admin">Super Admin</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="btn-primary w-full">
                    <i class="ph ph-paper-plane-right"></i> Salvar e Enviar Convite
                </button>
            </div>
        </form>
    </div>

    <!-- TABELA DE USUÁRIOS COM BUSCA E PAGINAÇÃO -->
    <div class="card table-container mb-4">
        
        <div class="table-header-actions">
            <h4 style="margin: 0;">Lista de Usuários</h4>
            
            <form action="/financas/usuarios" method="GET" class="search-form">
                <div class="input-with-icon" style="width: 100%;">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou e-mail..." value="<?= htmlspecialchars($busca ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <button type="submit" class="btn-primary" style="padding: 0 16px;">Buscar</button>
                <?php if (!empty($busca)): ?>
                    <a href="/financas/usuarios" class="btn-outline" style="padding: 0 16px; display: flex; align-items: center;" title="Limpar Busca"><i class="ph ph-x"></i></a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Status / Perfil</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($usuarios) > 0): ?>
                        <?php foreach ($usuarios as $item): ?>
                            <tr>
                                <td class="text-secondary font-medium">#<?= $item['id_usuario'] ?></td>
                                <td class="font-medium"><?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                
                                <td>
                                    <?php if (isset($item['status']) && $item['status'] == 'inativo'): ?>
                                        <span class="badge badge-danger" style="margin-right: 4px;"><i class="ph ph-user-minus"></i> Inativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-success" style="margin-right: 4px;"><i class="ph ph-user-check"></i> Ativo</span>
                                    <?php endif; ?>

                                    <?php if ($item['perfil'] == 'super_admin'): ?>
                                        <span class="badge" style="background: var(--color-purple); color: white;"><i class="ph-fill ph-crown"></i> Super Admin</span>
                                    <?php elseif ($item['perfil'] == 'admin'): ?>
                                        <span class="badge badge-admin"><i class="ph-fill ph-shield-star"></i> Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-common"><i class="ph ph-user"></i> Comum</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-right">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        
                                        <?php if (!($_SESSION['perfil'] === 'admin' && $item['perfil'] === 'super_admin')): ?>
                                            
                                            <a href="/financas/usuarios/edit/<?= $item['id_usuario'] ?>" class="icon-btn-sm" title="Editar">
                                                <i class="ph ph-pencil-simple"></i>
                                            </a>
                                            
                                            <?php if (isset($item['status']) && $item['status'] == 'inativo'): ?>
                                                <form action="/financas/usuarios/reativar/<?= $item['id_usuario'] ?>" method="POST" style="margin: 0;">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                    <button type="submit" class="icon-btn-sm" title="Reativar Usuário" onclick="return confirm('Deseja reativar o acesso deste usuário?');" style="color: var(--color-emerald);">
                                                        <i class="ph ph-check-circle"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <?php if ($item['perfil'] !== 'super_admin'): ?>
                                                    <form action="/financas/usuarios/delete/<?= $item['id_usuario'] ?>" method="POST" style="margin: 0;">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                        <button type="submit" class="icon-btn-sm danger" title="Bloquear Usuário" onclick="return confirm('Deseja bloquear este usuário?');">
                                                            <i class="ph ph-prohibit"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <span class="text-secondary" style="font-size: 12px; font-style: italic;">Restrito</span>
                                        <?php endif; ?>
                                        
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-secondary);">
                                <i class="ph ph-users" style="font-size: 32px; opacity: 0.5; margin-bottom: 8px; display: block;"></i>
                                Nenhum usuário encontrado com estes critérios.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Renderização da Paginação -->
        <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
            <ul class="pagination">
                <?php 
                $termoBusca = !empty($busca) ? '&busca=' . urlencode($busca) : '';
                
                // Botão Anterior
                if ($paginaAtual > 1): ?>
                    <li><a href="?pagina=<?= ($paginaAtual - 1) . $termoBusca ?>" class="pagination-link"><i class="ph ph-caret-left"></i></a></li>
                <?php else: ?>
                    <li><span class="pagination-disabled"><i class="ph ph-caret-left"></i></span></li>
                <?php endif; ?>

                <!-- Números das Páginas -->
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li>
                        <a href="?pagina=<?= $i . $termoBusca ?>" class="pagination-link <?= ($i == $paginaAtual) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <!-- Botão Próximo -->
                <?php if ($paginaAtual < $totalPaginas): ?>
                    <li><a href="?pagina=<?= ($paginaAtual + 1) . $termoBusca ?>" class="pagination-link"><i class="ph ph-caret-right"></i></a></li>
                <?php else: ?>
                    <li><span class="pagination-disabled"><i class="ph ph-caret-right"></i></span></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

    </div>
</div>