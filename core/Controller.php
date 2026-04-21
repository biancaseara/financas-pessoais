<?php

class Controller {
    
    public function model($model) {
        $model = preg_replace('/[^a-zA-Z0-9_]/', '', $model); // Sanitiza o nome do model, permitindo apenas caracteres alfanuméricos e underscores

        $arquivo = BASE_PATH . '/app/Models/' . $model . '.php';

        if (file_exists($arquivo)) {
            require_once $arquivo;
            return new $model();
        } else {
            throw new Exception("Erro Crítico: O Model '{$model}' não foi encontrado no servidor.");
        }
    }

    public function view($viewName, $dados = []) {
        $viewName = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $viewName); // Sanitiza o nome da view, permitindo apenas caracteres alfanuméricos, underscores, hífens e barras

        $viewName = str_replace(['..', '../'], '', $viewName);

        // O EXTR_SKIP evita que variáveis já existentes sejam sobrescritas pelos dados da view
        extract($dados, EXTR_SKIP);
        
        $arquivoView = BASE_PATH . '/app/Views/' . $viewName . '.php';
        $arquivoTemplate = BASE_PATH . '/app/Views/template.php';
        
        if (file_exists($arquivoView) && file_exists($arquivoTemplate)) {
            // O template.php vai ser carregado e, dentro dele, chamaremos a $arquivoView
            require_once $arquivoTemplate;
        } else {
            throw new Exception("Erro de View: A tela '{$viewName}' solicitada não existe.");
        }
    }
}

?>