<?php

class Categoria {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function listarTodos($id_usuario) {
        $sql = "SELECT * FROM categorias WHERE id_usuario = ? ORDER BY tipo, nome_categoria";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id, $id_usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id_categoria = ? AND id_usuario = ?");
        $stmt->execute([$id, $id_usuario]);
        return $stmt->fetch();
    }

    public function cadastrar($id_usuario, $nome, $tipo, $limite_mensal = null) {
        $nome = strip_tags(trim($nome));
        $sql = "INSERT INTO categorias (id_usuario, nome_categoria, tipo, limite_mensal) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario, $nome, $tipo, $limite_mensal]);
    }

    public function atualizar($id, $id_usuario, $nome, $tipo, $limite_mensal = null) {
        $nome = strip_tags(trim($nome));
        $sql = "UPDATE categorias SET nome_categoria=?, tipo=?, limite_mensal=? WHERE id_categoria=? AND id_usuario=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nome, $tipo, $limite_mensal, $id, $id_usuario]);
    }

    public function deletar($id, $id_usuario) {
        $stmt = $this->pdo->prepare("DELETE FROM categorias WHERE id_categoria = ? AND id_usuario = ?");
        return $stmt->execute([$id, $id_usuario]);
    }
}