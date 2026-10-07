<?php

class Meta {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos($id_usuario) {
        $sql = "SELECT * FROM metas WHERE id_usuario = ? ORDER BY data_limite ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id, $id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM metas WHERE id_meta = ? AND id_usuario = ?");
        $stmt->execute([$id, $id_usuario]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $titulo_meta, $valor_objetivo, $valor_atual, $data_limite) {
        $sql = "INSERT INTO metas (id_usuario, titulo_meta, valor_objetivo, valor_atual, data_limite) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $titulo_meta, $valor_objetivo, $valor_atual, $data_limite]);
    }

    public function atualizar($id, $id_usuario, $titulo_meta, $valor_objetivo, $valor_atual, $data_limite) {
        $sql = "UPDATE metas SET titulo_meta=?, valor_objetivo=?, valor_atual=?, data_limite=? WHERE id_meta=? AND id_usuario=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titulo_meta, $valor_objetivo, $valor_atual, $data_limite, $id, $id_usuario]);
    }

    public function deletar($id, $id_usuario) {
        $stmt = $this->pdo->prepare("DELETE FROM metas WHERE id_meta = ? AND id_usuario = ?");
        return $stmt->execute([$id, $id_usuario]);
    }
    public function listarHistorico($id_meta) {
        $stmt = $this->pdo->prepare("SELECT * FROM historico_metas WHERE id_meta = ? ORDER BY data_movimento DESC, id_historico DESC");
        $stmt->execute([$id_meta]);
        return $stmt->fetchAll();
    }

    public function adicionarAporte($id_meta, $id_usuario, $valor, $data_movimento, $observacao) {
        $meta = $this->buscarPorId($id_meta, $id_usuario);
        if (!$meta) return false;

        $sql = "INSERT INTO historico_metas (id_meta, valor, data_movimento, observacao) VALUES (?, ?, ?, ?)";
        $this->pdo->prepare($sql)->execute([$id_meta, $valor, $data_movimento, $observacao]);

        $novo_valor_atual = $meta['valor_atual'] + $valor;
        $sqlUpdate = "UPDATE metas SET valor_atual = ? WHERE id_meta = ?";
        $this->pdo->prepare($sqlUpdate)->execute([$novo_valor_atual, $id_meta]);

        return true;
    }
}
?>