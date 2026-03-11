<?php
if (isset($_SESSION['id_usuario'])) {
    header("Location: /financas");
    exit();
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!empty($email) && !empty($senha)) {
        try {
            $dsn = 'mysql:dbname=financas_pessoais;port=3306;host=localhost';
            $pdo = new PDO($dsn, 'admin', '@admin123');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['perfil'] = $usuario['perfil'];
                header("Location: /financas");
                exit();
            } else {
                $erro = "Email ou senha inválidos.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no banco de dados: " . $e->getMessage();
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Financeiro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 300px;
            text-align: center;
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #555;
        }
        .erro {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Acessar Sistema</h2>
        
        <?php if ($erro): ?>
            <div class="erro"><?= $erro ?></div>
        <?php endif; ?>

        <form action="/financas/login" method="POST">
            <input type="email" name="email" placeholder="Seu E-mail" required>
            <input type="password" name="senha" placeholder="Sua Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>

</body>
</html>