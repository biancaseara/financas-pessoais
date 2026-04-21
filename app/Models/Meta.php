<?php

class Meta {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos() {
        $sql = "SELECT * FROM metas ORDER BY data_limite ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM metas WHERE id_meta = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $titulo_meta, $valor_objetivo, $valor_atual, $data_limite) {
        $sql = "INSERT INTO metas (id_usuario, titulo_meta, valor_objetivo, valor_atual, data_limite) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $titulo_meta, $valor_objetivo, $valor_atual, $data_limite]);
    }

    public function atualizar($id, $titulo_meta, $valor_objetivo, $valor_atual, $data_limite) {
        $sql = "UPDATE metas SET titulo_meta=?, valor_objetivo=?, valor_atual=?, data_limite=? WHERE id_meta=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titulo_meta, $valor_objetivo, $valor_atual, $data_limite, $id]);
    }

    public function deletar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM metas WHERE id_meta = ?");
        return $stmt->execute([$id]);
    }
}