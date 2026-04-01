<?php
// Modelo de ferramentas
class Ferramenta extends ModeloBase
{
    public function listar()
    {
        return $this->db->query('SELECT * FROM ferramentas ORDER BY nome')->fetchAll();
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM ferramentas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare('INSERT INTO ferramentas (nome, descricao, status) VALUES (:nome, :descricao, :status)');
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':descricao' => $dados['descricao'],
            ':status' => $dados['status']
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
