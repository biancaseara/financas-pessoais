<?php

class Env {
    public static function load($path) {
        if (!file_exists($path)) {
            die("Erro: Arquivo .env não encontrado.");
        }

        $linhas = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($linhas as $linha) {
            // Ignora linhas de comentário (que começam com #)
            if (strpos(trim($linha), '#') === 0) continue;
            
            // Divide o nome da variável do valor
            list($nome, $valor) = explode('=', $linha, 2);
            $nome = trim($nome);
            $valor = trim($valor);
            
            // Grava na memória do servidor para usarmos depois
            if (!array_key_exists($nome, $_SERVER) && !array_key_exists($nome, $_ENV)) {
                $_ENV[$nome] = $valor;
            }
        }
    }
}