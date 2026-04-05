<?php
// Modelo de emprestimos de ferramentas
class EmprestimoFerramenta extends ModeloBase
{
    public function listar($limite = 50, $offset = 0)
    {
        $sql = 'SELECT e.*, f.nome AS ferramenta_nome,
                       ue.nome AS usuario_executor_nome,
                       ud.nome AS usuario_devolucao_nome
                FROM emprestimos_ferramentas e
                LEFT JOIN ferramentas f ON f.id = e.ferramenta_id
                LEFT JOIN usuarios ue ON ue.id = e.usuario_executor_id
                LEFT JOIN usuarios ud ON ud.id = e.usuario_devolucao_id
                ORDER BY e.data_retirada DESC';

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
        $linha = $this->db->query('SELECT COUNT(*) AS total FROM emprestimos_ferramentas')->fetch();
        return (int)($linha['total'] ?? 0);
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO emprestimos_ferramentas
                (ferramenta_id, usuario_responsavel, usuario_executor_id, data_retirada, data_devolucao, status)
             VALUES
                (:ferramenta_id, :usuario_responsavel, :usuario_executor_id, NOW(), :data_devolucao, :status)'
        );
        $stmt->execute([
            ':ferramenta_id' => $dados['ferramenta_id'],
            ':usuario_responsavel' => $dados['usuario_responsavel'],
            ':usuario_executor_id' => $dados['usuario_executor_id'],
            ':data_devolucao' => $dados['data_devolucao'],
            ':status' => $dados['status']
        ]);
        return $this->db->lastInsertId();
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM emprestimos_ferramentas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function registrarDevolucao($id, $usuarioDevolucaoId)
    {
        $stmt = $this->db->prepare(
            'UPDATE emprestimos_ferramentas
             SET status = \'Devolvida\',
                 data_devolucao = NOW(),
                 usuario_devolucao_id = :usuario_devolucao_id
             WHERE id = :id'
        );
        return $stmt->execute([
            ':usuario_devolucao_id' => $usuarioDevolucaoId,
            ':id' => $id
        ]);
    }
}
