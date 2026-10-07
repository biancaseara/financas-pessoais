<?php

class Investimento {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos($id_usuario) {
        $sql = "SELECT * FROM investimentos WHERE id_usuario = ? ORDER BY data_aplicacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id, $id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM investimentos WHERE id_investimento = ? AND id_usuario = ?");
        $stmt->execute([$id, $id_usuario]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $nome, $tipo, $corretora, $valor, $data_aplicacao, $vencimento) {
        $sql = "INSERT INTO investimentos (id_usuario, nome_investimento, tipo, corretora, valor_aplicado, data_aplicacao, vencimento) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario, $nome, $tipo, $corretora, $valor, $data_aplicacao, $vencimento]);
        
        $id_investimento = $this->pdo->lastInsertId();

        $sqlHist = "INSERT INTO historico_investimentos (id_investimento, tipo_movimento, valor, data_movimento, observacao) VALUES (?, 'Aporte', ?, ?, 'Aporte inicial')";
        $this->pdo->prepare($sqlHist)->execute([$id_investimento, $valor, $data_aplicacao]);

        return true;
    }

    public function atualizar($id, $id_usuario, $nome, $tipo, $corretora, $valor, $data_aplicacao, $vencimento) {
        $sql = "UPDATE investimentos SET nome_investimento=?, tipo=?, corretora=?, valor_aplicado=?, data_aplicacao=?, vencimento=? WHERE id_investimento=? AND id_usuario=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nome, $tipo, $corretora, $valor, $data_aplicacao, $vencimento, $id, $id_usuario]);
    }

    public function deletar($id, $id_usuario) {
        $stmt = $this->pdo->prepare("DELETE FROM investimentos WHERE id_investimento = ? AND id_usuario = ?");
        return $stmt->execute([$id, $id_usuario]);
    }

    public function listarTipos($id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM tipos_investimento WHERE id_usuario = ? ORDER BY categoria_grupo, nome_tipo");
        $stmt->execute([$id_usuario]);
        $tipos = $stmt->fetchAll();

        if (empty($tipos)) {
            $this->cadastrarTipo($id_usuario, 'Tesouro Direto', 'Renda Fixa');
            $this->cadastrarTipo($id_usuario, 'CDB / RDB', 'Renda Fixa');
            $this->cadastrarTipo($id_usuario, 'Poupança', 'Renda Fixa');
            $this->cadastrarTipo($id_usuario, 'Ações', 'Renda Variável');
            $this->cadastrarTipo($id_usuario, 'FIIs (Fundos Imobiliários)', 'Renda Variável');
            $this->cadastrarTipo($id_usuario, 'Criptomoedas', 'Renda Variável');
            $this->cadastrarTipo($id_usuario, 'Outros', 'Outros');

            $stmt->execute([$id_usuario]);
            $tipos = $stmt->fetchAll();
        }

        return $tipos;
    }

    public function cadastrarTipo($id_usuario, $nome_tipo, $grupo) {
        $stmt = $this->pdo->prepare("INSERT INTO tipos_investimento (id_usuario, nome_tipo, categoria_grupo) VALUES (?, ?, ?)");
        return $stmt->execute([$id_usuario, $nome_tipo, $grupo]);
    }
}
?>