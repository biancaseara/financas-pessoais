<?php

class Controller {
    
    public function model($model) {
        $arquivo = BASE_PATH . '/app/Models/' . $model . '.php';
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return new $model();
        } else {
            die("Erro: O Model '$model' não existe.");
        }
    }

    public function view($viewName, $dados = []) {
        extract($dados);
        
        $arquivoView = BASE_PATH . '/app/Views/' . $viewName . '.php';
        $arquivoTemplate = BASE_PATH . '/app/Views/template.php';
        
        if (file_exists($arquivoView) && file_exists($arquivoTemplate)) {
            // O template.php vai ser carregado e, dentro dele, chamaremos a $arquivoView
            require_once $arquivoTemplate;
        } else {
            die("Erro: A View '$viewName' ou o Template não existem.");
        }
    }
}