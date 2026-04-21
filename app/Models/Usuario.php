<?php

class Usuario {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function buscarPorEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function cadastrar($nome, $email, $senhaHash, $aceitou_termos, $data_aceite_termos, $perfil = 'comum') {
        try {
            $sql = "INSERT INTO usuarios (nome, email, senha, aceitou_termos, data_aceite_termos, perfil, data_cadastro) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nome, $email, $senhaHash, $aceitou_termos, $data_aceite_termos, $perfil]);
        } catch (PDOException $e) {
            // Retorna falso se o e-mail já existir (código 23000)
            return false;
        }
    }

    public function listarTodos() {
        return $this->pdo->query("SELECT * FROM usuarios")->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function atualizar($id, $nome, $email, $senha, $perfil) {
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome=?, email=?, senha=?, perfil=? WHERE id_usuario=?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nome, $email, $senhaHash, $perfil, $id]);
        } else {
            $sql = "UPDATE usuarios SET nome=?, email=?, perfil=? WHERE id_usuario=?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nome, $email, $perfil, $id]);
        }
    }

    public function deletar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false; // Retorna falso se houver dados vinculados a este usuário
        }
    }
}