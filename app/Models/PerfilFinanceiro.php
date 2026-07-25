<?php

class PerfilFinanceiro {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function salvarOnboardingInicial($id_usuario, $sentimento, $conhecimento, $renda_exata, $tipo_renda, $tem_dividas, $objetivo) {
        $stmt = $this->pdo->prepare("SELECT id_perfil FROM perfil_financeiro WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $existe = $stmt->fetch();

        if ($existe) {
            $sql = "UPDATE perfil_financeiro 
                    SET sentimento_dinheiro = ?, conhecimento_financeiro = ?, renda_exata = ?, tipo_renda = ?, tem_dividas = ?, objetivo_principal = ? 
                    WHERE id_usuario = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$sentimento, $conhecimento, $renda_exata, $tipo_renda, $tem_dividas, $objetivo, $id_usuario]);
        } else {
            $sql = "INSERT INTO perfil_financeiro 
                    (id_usuario, sentimento_dinheiro, conhecimento_financeiro, renda_exata, tipo_renda, tem_dividas, objetivo_principal) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_usuario, $sentimento, $conhecimento, $renda_exata, $tipo_renda, $tem_dividas, $objetivo]);
        }
    }

    public function atualizarPerfilCompleto($id_usuario, $maior_problema, $situacao_fim_mes, $tipos_divida, $status_divida, $valor_divida, $controle_gastos, $gatilho_gastos, $tentou_organizar, $tentou_nao_funcionou, $reserva_emergencia, $meses_reserva, $local_reserva, $conhece_conceitos, $ja_investiu, $tipos_investimento, $quer_renda_extra, $pode_aumentar_renda, $habilidades, $horas_disponiveis, $acesso_tecnologia, $dependentes, $tempo_melhoria, $sentimento_dinheiro, $conhecimento_financeiro, $renda_exata, $tipo_renda, $tem_dividas, $objetivo_principal) {
        
        $stmt = $this->pdo->prepare("SELECT id_perfil FROM perfil_financeiro WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $existe = $stmt->fetch();

        if ($existe) {
            $sql = "UPDATE perfil_financeiro SET 
                    maior_problema = ?, situacao_fim_mes = ?, tipos_divida = ?, status_divida = ?, valor_divida = ?, 
                    controle_gastos = ?, gatilho_gastos = ?, tentou_organizar = ?, tentou_nao_funcionou = ?, 
                    reserva_emergencia = ?, meses_reserva = ?, local_reserva = ?, conhece_conceitos = ?, 
                    ja_investiu = ?, tipos_investimento = ?, quer_renda_extra = ?, pode_aumentar_renda = ?, 
                    habilidades = ?, horas_disponiveis = ?, acesso_tecnologia = ?, dependentes = ?, tempo_melhoria = ?,
                    sentimento_dinheiro = ?, conhecimento_financeiro = ?, renda_exata = ?, tipo_renda = ?, tem_dividas = ?, objetivo_principal = ?
                    WHERE id_usuario = ?";
            
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                $maior_problema, $situacao_fim_mes, $tipos_divida, $status_divida, $valor_divida, 
                $controle_gastos, $gatilho_gastos, $tentou_organizar, $tentou_nao_funcionou, 
                $reserva_emergencia, $meses_reserva, $local_reserva, $conhece_conceitos, 
                $ja_investiu, $tipos_investimento, $quer_renda_extra, $pode_aumentar_renda, 
                $habilidades, $horas_disponiveis, $acesso_tecnologia, $dependentes, $tempo_melhoria, 
                $sentimento_dinheiro, $conhecimento_financeiro, $renda_exata, $tipo_renda, $tem_dividas, $objetivo_principal,
                $id_usuario
            ]);
        }
        return false;
    }

    public function buscarPorIdUsuario($id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM perfil_financeiro WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}