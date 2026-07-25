<?php
require_once BASE_PATH . '/core/Controller.php';

class IaController extends Controller {

    public function analisar() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /financas/auth/login");
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];

        $perfilModel = $this->model('PerfilFinanceiro');
        $perfil = $perfilModel->buscarPorIdUsuario($id_usuario);

        $dashboardModel = $this->model('Dashboard');
        $resumo = $dashboardModel->getResumo($id_usuario);

        $conselhoModel = $this->model('ConselhoIa');
        $historicoConselhos = $conselhoModel->buscarUltimosConselhos($id_usuario, 3);

        $prompt = "Aja como um instrutor financeiro educado, empático e direto. ";
        $prompt .= "O usuário preencheu um raio-x completo da sua vida financeira:\n\n";

        // Dados básicos e realidae financeira
        $prompt .= "- Renda Mensal: " . ($perfil['renda_mensal'] ?? 'Não informada') . " (" . ($perfil['tipo_renda'] ?? 'Não informada') . ").\n";
        $prompt .= "- Dependentes: " . ($perfil['dependentes'] ?? 'Não informado') . ".\n";
        $prompt .= "- Objetivo principal: " . ($perfil['objetivo_principal'] ?? 'Não informado') . " (Espera melhoria em: " . ($perfil['tempo_melhoria'] ?? 'Não informado') . ").\n";
        $prompt .= "- Sentimento atual: " . ($perfil['sentimento_dinheiro'] ?? 'Não informado') . ".\n\n";

        // Dívidas
        $prompt .= "- Maior desafio: " . ($perfil['maior_problema'] ?? 'Não informado') . ".\n";
        $prompt .= "- Sobra dinheiro no fim do mês? " . ($perfil['situacao_fim_mes'] ?? 'Não informada') . ".\n";
        $prompt .= "- Possui dívidas? " . ($perfil['tem_dividas'] ?? 'Não') . " (Tipos: " . ($perfil['tipos_divida'] ?? 'Nenhum') . ", Status: " . ($perfil['status_divida'] ?? 'N/A') . ", Valor Total: " . ($perfil['valor_divida'] ?? 'N/A') . ").\n\n";

        // Hábitos
        $prompt .= "- Como controla os gastos: " . ($perfil['controle_gastos'] ?? 'Não informado') . ".\n";
        $prompt .= "- Gatilho que faz gastar por impulso: " . ($perfil['gatilho_gastos'] ?? 'Não informado') . ".\n";
        $prompt .= "- O que já tentou fazer que não funcionou: " . ($perfil['tentou_nao_funcionou'] ?? 'Não informado') . ".\n\n";

        // Reserva, nível de conhecimento e investimentos
        $prompt .= "- Nível de conhecimento: " . ($perfil['conhecimento_financeiro'] ?? 'Iniciante') . " (Entende o básico de economia? " . ($perfil['conhece_conceitos'] ?? 'Não') . ").\n";
        $prompt .= "- Reserva de emergência: " . ($perfil['reserva_emergencia'] ?? 'Não possui') . " (Local: " . ($perfil['local_reserva'] ?? 'N/A') . ", Cobre quantos meses: " . ($perfil['meses_reserva'] ?? 'Nenhum') . ").\n";
        $prompt .= "- Produtos que investe: " . ($perfil['tipos_investimento'] ?? 'Nenhum') . ".\n\n";

        // Renda extra e habilidades
        $prompt .= "- Interesse em Renda Extra: " . ($perfil['quer_renda_extra'] ?? 'Não') . " (Tempo livre: " . ($perfil['horas_disponiveis'] ?? 'Nenhum') . " | Habilidade: " . ($perfil['habilidades'] ?? 'Nenhuma') . ").\n\n";

        $prompt .= "Neste mês atual, o balanço real dele no sistema está assim: Entrou R$ " . $resumo['entrada'] . " e Saiu R$ " . $resumo['saida'] . ".\n\n";
        
        $prompt .= "Neste mês atual, o balanço real dele no sistema está assim: Entrou R$ " . $resumo['entrada'] . " e Saiu R$ " . $resumo['saida'] . ".\n\n";

        // Memória de conselhos anteriores
        if (!empty($historicoConselhos)) {
            $prompt .= "--- HISTÓRICO DE CONSELHOS ANTERIORES ---\n";
            $prompt .= "Aqui estão os últimos conselhos que você (a IA) deu para este usuário recentemente. Leia para ter contexto e evitar repetir exatamente as mesmas dicas, buscando criar um senso de evolução:\n";
            foreach ($historicoConselhos as $index => $conselhoAntigo) {
                $prompt .= ($index + 1) . ". \"" . $conselhoAntigo['mensagem'] . "\"\n";
            }
            $prompt .= "-----------------------------------------\n\n";
        }

        $prompt .= "Instrução: Baseado no cenário acima e no seu histórico de dicas, escreva um conselho curto (máximo de 3 parágrafos) focando na realidade dele. Identifique o principal erro comportamental e sugira uma ação prática imediata. Não use formatação markdown complexa, use a tag HTML <strong> para destaques.";

        $chaveApi = getenv('GEMINI_API_KEY') ?: $_ENV['GEMINI_API_KEY'];
        $chaveApi = trim($chaveApi, " '\"\t\n\r\0\x0B"); 
        
        if (!$chaveApi) {
            die("Erro Crítico: A Chave de API do Gemini não foi encontrada no arquivo .env.");
        }

        $dados = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ]
        ];

        // Sistema de Fallback
        $modelosDisponiveis = [
            'gemini-flash-latest',
            'gemini-2.5-flash-lite',
            'gemini-2.0-flash'
        ];

        $mensagemIA = "Poxa, os servidores da inteligência artificial estão superlotados agora. Tente novamente em alguns minutos!";

        foreach ($modelosDisponiveis as $modelo) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key=" . $chaveApi;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); 

            $resposta = curl_exec($ch);
            $erroCurl = curl_error($ch); 
            curl_close($ch);

            if ($erroCurl) {
                continue; 
            }

            $resultado = json_decode($resposta, true);

            if (isset($resultado['candidates'][0]['content']['parts'][0]['text'])) {
                $mensagemIA = $resultado['candidates'][0]['content']['parts'][0]['text'];
                break; 
            }
            
            if (isset($resultado['error']['message'])) {
                $mensagemIA = "Erro da API do Google ({$modelo}): " . $resultado['error']['message'];
            }
        }

        $conselhoModel = $this->model('ConselhoIa');
        $conselhoModel->salvarConselho($id_usuario, $mensagemIA);

        header("Location: /financas/dashboard");
        exit;
    }
}