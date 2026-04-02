<?php
// Modelo de convites para cadastro de usuarios
class ConviteUsuario extends ModeloBase
{
    public function criar($dados)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO convites_usuarios
                (email, nome_sugerido, tipo_usuario, token_hash, status, usuario_convite_id, expira_em, criado_em)
             VALUES
                (:email, :nome_sugerido, :tipo_usuario, :token_hash, "pendente", :usuario_convite_id, :expira_em, NOW())'
        );
        $stmt->execute([
            ':email' => $dados['email'],
            ':nome_sugerido' => $dados['nome_sugerido'],
            ':tipo_usuario' => $dados['tipo_usuario'],
            ':token_hash' => $dados['token_hash'],
            ':usuario_convite_id' => $dados['usuario_convite_id'],
            ':expira_em' => $dados['expira_em']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function listarRecentes($limite = 20)
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, u.nome AS convidado_por_nome
             FROM convites_usuarios c
             LEFT JOIN usuarios u ON u.id = c.usuario_convite_id
             ORDER BY c.criado_em DESC
             LIMIT :limite'
        );
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarValidoPorToken($token)
    {
        $tokenHash = hash('sha256', $token);
        $stmt = $this->db->prepare(
            'SELECT *
             FROM convites_usuarios
             WHERE token_hash = :token_hash
               AND status = "pendente"
               AND usado_em IS NULL
               AND expira_em >= NOW()
             LIMIT 1'
        );
        $stmt->execute([':token_hash' => $tokenHash]);
        return $stmt->fetch() ?: null;
    }

    public function marcarComoAceito($id, $usuarioCriadoId)
    {
        $stmt = $this->db->prepare(
            'UPDATE convites_usuarios
             SET status = "aceito",
                 usuario_criado_id = :usuario_criado_id,
                 usado_em = NOW()
             WHERE id = :id
               AND status = "pendente"'
        );
        return $stmt->execute([
            ':usuario_criado_id' => $usuarioCriadoId,
            ':id' => $id
        ]);
    }
}
