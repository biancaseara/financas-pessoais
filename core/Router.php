<?php

class Router {
    public function run() {
        $uri = $_SERVER['REQUEST_URI'];
        
        $uri = explode('?', $uri)[0];
        $uri = trim($uri, '/');

        $partes = explode('/', $uri);

        if (isset($partes[0]) && $partes[0] === 'financas') {
            array_shift($partes);
        }

        $ctrlEntrada = isset($partes[0]) ? preg_replace('/[^a-zA-Z0-9]/', '', $partes[0]) : '';
        $controllerName = !empty($ctrlEntrada) ? ucfirst($ctrlEntrada) . 'Controller' : 'DashboardController';

        $metodoEntrada = isset($partes[1]) ? preg_replace('/[^a-zA-Z0-9]/', '', $partes[1]) : '';
        $methodName = !empty($metodoEntrada) ? $metodoEntrada : 'index';

        $params = array_slice($partes, 2);

        $controllerFile = BASE_PATH . '/app/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();


            if (is_callable([$controller, $methodName])) {
                call_user_func_array([$controller, $methodName], $params);
            } else {
                echo "<h1>Erro 404</h1><p>Método '" . htmlspecialchars($methodName) . "' não encontrado em " . htmlspecialchars($controllerName) . ".</p>";
            }
        } else {
            echo "<h1>Erro 404</h1><p>Página não encontrada (" . htmlspecialchars($controllerName) . ").</p>";
        }
    }
}