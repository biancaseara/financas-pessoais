<?php

class Controller {
    
    public function model($model) {
        $model = preg_replace('/[^a-zA-Z0-9_]/', '', $model); 
        $arquivo = BASE_PATH . '/app/Models/' . $model . '.php';

        if (file_exists($arquivo)) {
            require_once $arquivo;
            return new $model();
        } else {
            throw new Exception("Erro Crítico: O Model '{$model}' não foi encontrado no servidor.");
        }
    }

    public function view($viewName, $dados = [], $usarTemplate = true) {
        $viewName = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $viewName); 
        $viewName = str_replace(['..', '../'], '', $viewName);

        extract($dados, EXTR_SKIP);
        
        $arquivoView = BASE_PATH . '/app/Views/' . $viewName . '.php';
        
        if (file_exists($arquivoView)) {
            if ($usarTemplate) {
                $arquivoTemplate = BASE_PATH . '/app/Views/template.php';
                if (file_exists($arquivoTemplate)) {
                    require_once $arquivoTemplate;
                } else {
                    throw new Exception("Erro: O template.php não foi encontrado.");
                }
            } else {
                // Se não for para usar o template, carrega a view diretamente
                require_once $arquivoView;
            }
        } else {
            throw new Exception("Erro de View: A tela '{$viewName}' solicitada não existe.");
        }
    }

    protected function exigirOnboarding() {
        if (isset($_SESSION['id_usuario'])) {
            $usuarioModel = $this->model('Usuario');
            $usuario = $usuarioModel->buscarPorId($_SESSION['id_usuario']);

            if ($usuario && (!isset($usuario['fez_onboarding']) || $usuario['fez_onboarding'] == 0)) {
                $urlAtual = $_SERVER['REQUEST_URI'];
                
                // Evita um loop infinito de redirecionamento checando se ele já está na rota do onboarding ou tentando sair
                if (strpos($urlAtual, '/onboarding') === false && strpos($urlAtual, '/auth/logout') === false) {
                    header("Location: /financas/onboarding");
                    exit;
                }
            }
        }
    }
}
?>