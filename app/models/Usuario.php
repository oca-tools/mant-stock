<?php
// Modelo de usuarios
class Usuario extends ModeloBase
{
    public function listar()
    {
        return $this->db->query('SELECT id, nome, email, tipo_usuario, ativo, created_at FROM usuarios ORDER BY nome')->fetchAll();
    }

    public function buscarPorEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email AND ativo = 1 LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare('INSERT INTO usuarios (nome, email, senha_hash, tipo_usuario, ativo, created_at) VALUES (:nome, :email, :senha_hash, :tipo_usuario, :ativo, NOW())');
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':email' => $dados['email'],
            ':senha_hash' => $dados['senha_hash'],
            ':tipo_usuario' => $dados['tipo_usuario'],
            ':ativo' => $dados['ativo']
        ]);
        return $this->db->lastInsertId();
    }

    public function atualizar($id, $dados)
    {
        $campos = [];
        $params = [':id' => $id];

        foreach (['nome', 'email', 'tipo_usuario', 'ativo', 'senha_hash'] as $campo) {
            if (isset($dados[$campo])) {
                $campos[] = $campo . ' = :' . $campo;
                $params[':' . $campo] = $dados[$campo];
            }
        }

        if (empty($campos)) {
            return false;
        }

        $sql = 'UPDATE usuarios SET ' . implode(', ', $campos) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
