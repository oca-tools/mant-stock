<?php
// Modelo de usuarios
class Usuario extends ModeloBase
{
    public function listar()
    {
        return $this->db->query('SELECT id, nome, email, tipo_usuario, ativo, created_at FROM usuarios ORDER BY nome')->fetchAll();
    }

    public function listarAtivosPorEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email AND ativo = 1 ORDER BY id ASC');
        $stmt->execute([':email' => $email]);
        return $stmt->fetchAll();
    }

    public function autenticarPorEmailSenha($email, $senha)
    {
        $usuarios = $this->listarAtivosPorEmail($email);
        foreach ($usuarios as $usuario) {
            if (password_verify($senha, $usuario['senha_hash'])) {
                return $usuario;
            }
        }
        return null;
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function contarPorEmail($email, $ignorarUsuarioId = null)
    {
        $sql = 'SELECT COUNT(*) AS total FROM usuarios WHERE email = :email';
        $params = [':email' => $email];
        if (!empty($ignorarUsuarioId)) {
            $sql .= ' AND id <> :ignorar_id';
            $params[':ignorar_id'] = $ignorarUsuarioId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $linha = $stmt->fetch();
        return (int)($linha['total'] ?? 0);
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

    public function senhaJaUtilizadaNoEmail($email, $senha, $ignorarUsuarioId = null)
    {
        $sql = 'SELECT id, senha_hash FROM usuarios WHERE email = :email';
        $params = [':email' => $email];
        if (!empty($ignorarUsuarioId)) {
            $sql .= ' AND id <> :ignorar_id';
            $params[':ignorar_id'] = $ignorarUsuarioId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $usuarios = $stmt->fetchAll();
        foreach ($usuarios as $usuario) {
            if (password_verify($senha, $usuario['senha_hash'])) {
                return true;
            }
        }
        return false;
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
