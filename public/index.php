<?php
session_start();

// Configuração de erros (remova ou comente quando colocar em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define o caminho base do sistema
define('BASE_PATH', dirname(__DIR__));

// Carrega as configurações e o Core MVC
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Router.php';

// Inicia o Roteador
$router = new Router();
$router->run();
?>