<?php

class Categoria {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos() {
        $sql = "SELECT * FROM categorias ORDER BY tipo, nome_categoria";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $nome, $tipo) {
        $sql = "INSERT INTO categorias (id_usuario, nome_categoria, tipo) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $nome, $tipo]);
    }

    public function atualizar($id, $nome, $tipo) {
        $sql = "UPDATE categorias SET nome_categoria=?, tipo=? WHERE id_categoria=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nome, $tipo, $id]);
    }

    public function deletar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        return $stmt->execute([$id]);
    }
}