<?php

trait ManagerTrait
{
    public function processarRequisicao($metodo, $uri, $putData = [])
    {
        $id = isset($uri[3]) && is_numeric($uri[3]) ? $uri[3] : null;

        switch ($metodo) {
            case "GET":
                if ($id) {
                    $this->listarUm($id);
                } else {
                    $this->listarTodos();
                }
                break;
            case "POST":
                $this->cadastrar($_POST);
                break;
            case "PUT":
                $this->atualizar($id, $putData);
                break;
            case "DELETE":
                $this->deletar($id);
                break;
            default:
                echo "Erro: Método HTTP não suportado.";
                break;
        }
    }
}
