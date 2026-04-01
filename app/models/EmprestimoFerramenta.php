<?php
// Modelo de emprestimos de ferramentas
class EmprestimoFerramenta extends ModeloBase
{
    public function listar($limite = 50)
    {
        $stmt = $this->db->prepare('SELECT e.*, f.nome AS ferramenta_nome FROM emprestimos_ferramentas e LEFT JOIN ferramentas f ON f.id = e.ferramenta_id ORDER BY e.data_retirada DESC LIMIT :limite');
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare('INSERT INTO emprestimos_ferramentas (ferramenta_id, usuario_responsavel, data_retirada, data_devolucao, status) VALUES (:ferramenta_id, :usuario_responsavel, NOW(), :data_devolucao, :status)');
        $stmt->execute([
            ':ferramenta_id' => $dados['ferramenta_id'],
            ':usuario_responsavel' => $dados['usuario_responsavel'],
            ':data_devolucao' => $dados['data_devolucao'],
            ':status' => $dados['status']
        ]);
        return $this->db->lastInsertId();
    }

    public function registrarDevolucao($id)
    {
        $stmt = $this->db->prepare('UPDATE emprestimos_ferramentas SET status = "Devolvida", data_devolucao = NOW() WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
