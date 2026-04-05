<?php
// Modelo de ferramentas
class Ferramenta extends ModeloBase
{
    public function listar($limite = 50, $offset = 0)
    {
        $sql = 'SELECT f.*, u.nome AS usuario_cadastro_nome
                FROM ferramentas f
                LEFT JOIN usuarios u ON u.id = f.usuario_cadastro_id
                ORDER BY f.nome ASC';

        if ($limite !== null) {
            $sql .= ' LIMIT :limite OFFSET :offset';
        }

        $stmt = $this->db->prepare($sql);
        if ($limite !== null) {
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contar()
    {
        $linha = $this->db->query('SELECT COUNT(*) AS total FROM ferramentas')->fetch();
        return (int)($linha['total'] ?? 0);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM ferramentas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function buscarPorIdParaAtualizacao($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM ferramentas WHERE id = :id LIMIT 1 FOR UPDATE');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO ferramentas (nome, descricao, status, usuario_cadastro_id)
             VALUES (:nome, :descricao, :status, :usuario_cadastro_id)'
        );
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':descricao' => $dados['descricao'],
            ':status' => $dados['status'],
            ':usuario_cadastro_id' => $dados['usuario_cadastro_id']
        ]);
        return $this->db->lastInsertId();
    }

    public function atualizarStatus($id, $status)
    {
        $stmt = $this->db->prepare('UPDATE ferramentas SET status = :status WHERE id = :id');
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }
}
