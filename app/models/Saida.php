<?php
// Modelo de saidas
class Saida extends ModeloBase
{
    public function listar($limite = 50, $offset = 0)
    {
        $sql = 'SELECT s.*, p.nome AS produto_nome, p.codigo_interno, p.unidade_medida, u.nome AS usuario_nome
                FROM saidas s
                LEFT JOIN produtos p ON p.id = s.produto_id
                LEFT JOIN usuarios u ON u.id = s.usuario_id
                ORDER BY s.data_saida DESC';
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
        $linha = $this->db->query('SELECT COUNT(*) AS total FROM saidas')->fetch();
        return (int)($linha['total'] ?? 0);
    }

    public function buscarDetalhadaPorId($id)
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, p.nome AS produto_nome, p.codigo_interno, p.unidade_medida, u.nome AS usuario_nome
             FROM saidas s
             LEFT JOIN produtos p ON p.id = s.produto_id
             LEFT JOIN usuarios u ON u.id = s.usuario_id
             WHERE s.id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO saidas (produto_id, quantidade, setor, local_utilizacao, tecnico_responsavel, usuario_id, data_saida, observacoes)
             VALUES (:produto_id, :quantidade, :setor, :local_utilizacao, :tecnico_responsavel, :usuario_id, NOW(), :observacoes)'
        );
        $stmt->execute([
            ':produto_id' => $dados['produto_id'],
            ':quantidade' => $dados['quantidade'],
            ':setor' => $dados['setor'],
            ':local_utilizacao' => $dados['local_utilizacao'],
            ':tecnico_responsavel' => $dados['tecnico_responsavel'],
            ':usuario_id' => $dados['usuario_id'],
            ':observacoes' => $dados['observacoes']
        ]);
        return $this->db->lastInsertId();
    }
}
