<?php

// Define a base do sistema para facilitar a inclusão de arquivos e evitar problemas de caminhos relativos.

use PSpell\Config;

define('BASE_PATH', dirname(__DIR__));

// Acorda com o ambiente, configure as variáveis de ambiente e as configurações dinâmicas de exibição de erros.
require_once BASE_PATH . '/core/Env.php';
Env::load(BASE_PATH . '/.env');

if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Modo de produção: desativa a exibição de erros para os usuários finais, mas ainda registra os erros em logs.
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Configuração de sessão personalizada para aumentar a segurança e a duração da sessão. O tempo de vida é definido para 30 dias, e as opções de cookie são configuradas para serem seguras, HTTP-only e com SameSite Strict para proteger contra ataques CSRF.
$tempoDeVida = 60 * 60 * 24 * 30; // 30 dias
$isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

session_set_cookie_params([
    'lifetime' => $tempoDeVida,
    'path' => '/',
    'domain' => '', // Deixe vazio para usar o domínio atual
    'secure' => $isSecure,
    'httponly' => true,
    'samesite' => 'Strict'
]);

ini_set('session.gc_maxlifetime', $tempoDeVida);

// Inicia a sessão para gerenciar o estado do usuário e outras informações persistentes durante a navegação.
session_start();

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Router.php';

// Inicia o Roteador
$router = new Router();
$router->run();
