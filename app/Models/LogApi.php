<?php

class LogApi {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function registrar($id_usuario, $endpoint, $status_code, $tokens_prompt, $tokens_completion, $tempo_resposta_ms) {
        $sql = "INSERT INTO logs_api (id_usuario, endpoint, status_code, tokens_prompt, tokens_completion, tempo_resposta_ms) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $id_usuario, 
            $endpoint, 
            $status_code, 
            $tokens_prompt, 
            $tokens_completion, 
            $tempo_resposta_ms
        ]);
    }

    public function obterMetricasGerais() {
        $sql = "SELECT 
                    COUNT(*) as total_requisicoes,
                    COALESCE(SUM(tokens_prompt + tokens_completion), 0) as total_tokens,
                    COALESCE(ROUND(AVG(tempo_resposta_ms)), 0) as latencia_media,
                    COALESCE(SUM(CASE WHEN status_code != 200 THEN 1 ELSE 0 END), 0) as total_erros
                FROM logs_api";
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function obterGrafico7Dias() {
        $sql = "SELECT DATE(data_requisicao) as data_dia, COUNT(*) as total_requisicoes, COALESCE(SUM(tokens_prompt + tokens_completion), 0) as total_tokens 
                FROM logs_api 
                WHERE data_requisicao >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY DATE(data_requisicao)
                ORDER BY DATE(data_requisicao) ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}