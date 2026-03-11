<?php
session_start();

// Configuração de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Tratamento da URL 
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('?', $uri)[0];
$uri = explode('/', $uri);
$rota = $uri[2] ?? '';

// Rotas Públicas (sem autenticação)
if ($rota == 'login') {
    require_once 'login.php';
    exit();
}
if ($rota == 'logout') {
    require_once 'logout.php';
    exit();
}

// Trava de segurança: redireciona para login se não estiver autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: /financas/login");
    exit();
}

// Conexão com o banco de dados 
try {
    $dsn = 'mysql:dbname=financas_pessoais;port=3306;host=localhost';
    $pdo = new PDO($dsn, 'admin', '@admin123');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

// Prepara os dados de PUT, se houver
$metodo = $_SERVER['REQUEST_METHOD'];
if ($metodo == 'PUT') parse_str(file_get_contents('php://input'), $_PUT);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Financeiro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .nav-bar {
            background: #333;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .nav-bar a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            font-weight: bold;
        }

        .nav-bar a:hover {
            color: #ddd;
        }

        .card-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            flex: 1;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .valor-verde {
            color: green;
            font-size: 24px;
            font-weight: bold;
        }

        .valor-vermelho {
            color: red;
            font-size: 24px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>

<body>
    <!-- Menu -->
    <div class="nav-bar">
        <a href="/financas">🏠 Dashboard</a>
        <a href="/financas/transacoes">💸 Transações</a>
        <a href="/financas/contas">🏦 Contas</a>
        <a href="/financas/categorias">📂 Categorias</a>
        <a href="/financas/metas">🎯 Metas</a>

        <?php if (isset($_SESSION['perfil']) && $_SESSION['perfil'] == 'admin'): ?>
            <a href="/financas/usuarios">👤 Usuários</a>
        <?php endif; ?>

        <a href="/financas/logout">🚪 Sair</a>
    </div>

    <?php
    if (isset($uri[2]) && !empty($uri[2])) {
        if ($uri[2] == 'contas') require_once 'contas.php';
        elseif ($uri[2] == 'categorias') require_once 'categorias.php';
        elseif ($uri[2] == 'transacoes') require_once 'transacoes.php';
        elseif ($uri[2] == 'metas') require_once 'metas.php';
        elseif ($uri[2] == 'usuarios') require_once 'usuarios.php';
        else echo "<h2>Página não encontrada</h2>";
    } else {
        // Dashboard
        $sqlEntrada = "SELECT SUM(valor) as total FROM transacoes WHERE tipo_transacao = 'Entrada'";
        $totalEntrada = $pdo->query($sqlEntrada)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $sqlSaida = "SELECT SUM(valor) as total FROM transacoes WHERE tipo_transacao = 'Saida'";
        $totalSaida = $pdo->query($sqlSaida)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $saldoGeral = $totalEntrada - $totalSaida;

        $sqlRecentes = "SELECT t.*, c.nome_banco, cat.nome_categoria 
        FROM transacoes t 
        JOIN contas c ON t.id_conta = c.id_conta 
        JOIN categorias cat ON t.id_categoria = cat.id_categoria 
        ORDER BY t.data_transacao DESC LIMIT 5";
        $recentes = $pdo->query($sqlRecentes)->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <h1>Resumo Financeiro</h1>

        <div class="card-container">
            <div class="card">
                <h3>Total Recebido</h3>
                <div class="valor-verde">R$ <?= number_format($totalEntrada, 2, ',', '.') ?></div>
            </div>
            <div class="card">
                <h3>Total Gasto</h3>
                <div class="valor-vermelho">R$ <?= number_format($totalSaida, 2, ',', '.') ?></div>
            </div>
            <div class="card">
                <h3>Balanço Geral</h3>
                <div style="font-size: 24px; font-weight: bold; color: <?= $saldoGeral >= 0 ? 'blue' : 'red' ?>;">
                    R$ <?= number_format($saldoGeral, 2, ',', '.') ?>
                </div>
            </div>
        </div>

        <h2>Últimas Movimentações</h2>
        <table>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Categoria</th>
                <th>Banco</th>
                <th>Valor</th>
            </tr>
            <?php foreach ($recentes as $r): ?>
                <?php $cor = ($r['tipo_transacao'] == 'Entrada') ? 'green' : 'red'; ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($r['data_transacao'])) ?></td>
                    <td><?= htmlspecialchars($r['descricao']) ?></td>
                    <td><?= htmlspecialchars($r['nome_categoria']) ?></td>
                    <td><?= htmlspecialchars($r['nome_banco'] ?? '—') ?></td>
                    <td style="color: <?= $cor ?>; font-weight: bold;">
                        R$ <?= number_format($r['valor'], 2, ',', '.') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    <?php
    }
    ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('form').on('submit', (e) => {
            formulario = e.target;
            metodo = $(formulario).attr('method');
            action = $(formulario).attr('action');
            if (metodo == 'PUT' || metodo == 'DELETE') {
                e.preventDefault();
                $.ajax({
                    url: action,
                    type: metodo,
                    data: $(formulario).serialize(),
                    success: (res) => {
                        let destino = $(formulario).data('redirect');
                        if (destino) {
                            window.location.href = destino;
                        } else {
                            location.reload();
                        }
                    },
                    error: (err) => {
                        alert('Erro: ' + err.responseText);
                    }
                });
            }
        })
    </script>
</body>

</html>