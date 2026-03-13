<?php

class Router {
    public function run() {
        // Pega a URL digitada
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query strings (ex: ?id=1) da URL base
        $uri = explode('?', $uri)[0];
        $uri = trim($uri, '/');
        
        // Quebra a URL em um array usando a barra como separador
        $partes = explode('/', $uri);

        // Se você acessa via "localhost/financas", o primeiro item é "financas"
        // Vamos remover essa parte para focar apenas no que importa
        if (isset($partes[0]) && $partes[0] === 'financas') {
            array_shift($partes);
        }

        // 1. Define o Controller (Padrão: DashboardController se estiver vazio)
        $controllerName = !empty($partes[0]) ? ucfirst($partes[0]) . 'Controller' : 'DashboardController';
        
        // 2. Define o Método (Padrão: index se estiver vazio)
        $methodName = !empty($partes[1]) ? $partes[1] : 'index';

        // 3. Define os Parâmetros Extras (ex: um ID de edição)
        $params = array_slice($partes, 2);

        // Caminho físico do arquivo do Controller
        $controllerFile = BASE_PATH . '/app/Controllers/' . $controllerName . '.php';

        // Verifica se o Controller existe e o executa
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = new $controllerName();

            // Verifica se o método existe dentro do Controller
            if (method_exists($controller, $methodName)) {
                // Executa o método passando os parâmetros
                call_user_func_array([$controller, $methodName], $params);
            } else {
                echo "<h1>Erro 404</h1><p>Método '$methodName' não encontrado em $controllerName.</p>";
            }
        } else {
            echo "<h1>Erro 404</h1><p>Página não encontrada ($controllerName).</p>";
        }
    }
}