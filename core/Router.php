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

        $controllerName = !empty($partes[0]) ? ucfirst($partes[0]) . 'Controller' : 'DashboardController';

        $methodName = !empty($partes[1]) ? $partes[1] : 'index';

        $params = array_slice($partes, 2);

        $controllerFile = BASE_PATH . '/app/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();


            if (method_exists($controller, $methodName)) {

                call_user_func_array([$controller, $methodName], $params);
            } else {
                echo "<h1>Erro 404</h1><p>Método '$methodName' não encontrado em $controllerName.</p>";
            }
        } else {
            echo "<h1>Erro 404</h1><p>Página não encontrada ($controllerName).</p>";
        }
    }
}