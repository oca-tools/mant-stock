<?php
// Modelo de movimentacoes
class Movimentacao extends ModeloBase
{
    public function listarRecentes($limite = 10)
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, p.nome AS produto_nome, u.nome AS usuario_nome
             FROM movimentacoes m
             LEFT JOIN produtos p ON p.id = m.produto_id
             LEFT JOIN usuarios u ON u.id = m.usuario_id
             ORDER BY m.data_movimentacao DESC
             LIMIT :limite'
        );
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO movimentacoes (produto_id, tipo_movimentacao, quantidade, usuario_id, origem, destino, observacoes, data_movimentacao)
             VALUES (:produto_id, :tipo_movimentacao, :quantidade, :usuario_id, :origem, :destino, :observacoes, NOW())'
        );
        $stmt->execute([
            ':produto_id' => $dados['produto_id'],
            ':tipo_movimentacao' => $dados['tipo_movimentacao'],
            ':quantidade' => $dados['quantidade'],
            ':usuario_id' => $dados['usuario_id'],
            ':origem' => $dados['origem'],
            ':destino' => $dados['destino'],
            ':observacoes' => $dados['observacoes']
        ]);
        return $this->db->lastInsertId();
    }

    public function listarPorPeriodo($inicio, $fim, $limite = null, $offset = 0)
    {
        $sql = 'SELECT m.*, p.nome AS produto_nome, u.nome AS usuario_nome
                FROM movimentacoes m
                LEFT JOIN produtos p ON p.id = m.produto_id
                LEFT JOIN usuarios u ON u.id = m.usuario_id
                WHERE DATE(m.data_movimentacao) BETWEEN :inicio AND :fim
                ORDER BY m.data_movimentacao DESC';
        if ($limite !== null) {
            $sql .= ' LIMIT :limite OFFSET :offset';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':inicio', $inicio, PDO::PARAM_STR);
        $stmt->bindValue(':fim', $fim, PDO::PARAM_STR);
        if ($limite !== null) {
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarPorPeriodo($inicio, $fim)
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) AS total
             FROM movimentacoes
             WHERE DATE(data_movimentacao) BETWEEN :inicio AND :fim'
        );
        $stmt->execute([
            ':inicio' => $inicio,
            ':fim' => $fim
        ]);
        $linha = $stmt->fetch();
        return (int)($linha['total'] ?? 0);
    }

    public function listarPorProduto($produtoId, $limite = 15)
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, u.nome AS usuario_nome
             FROM movimentacoes m
             LEFT JOIN usuarios u ON u.id = m.usuario_id
             WHERE m.produto_id = :produto_id
             ORDER BY m.data_movimentacao DESC
             LIMIT :limite'
        );
        $stmt->bindValue(':produto_id', (int)$produtoId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarPorProduto($produtoId)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM movimentacoes WHERE produto_id = :produto_id');
        $stmt->execute([':produto_id' => $produtoId]);
        $linha = $stmt->fetch();
        return (int)$linha['total'];
    }
}
