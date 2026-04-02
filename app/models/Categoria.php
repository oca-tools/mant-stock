<?php
// Modelo de categorias
class Categoria extends ModeloBase
{
    public function listar()
    {
        return $this->db->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM categorias WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare('INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)');
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':descricao' => $dados['descricao']
        ]);
        return $this->db->lastInsertId();
    }

    public function atualizar($id, $dados)
    {
        $stmt = $this->db->prepare('UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id');
        return $stmt->execute([
            ':nome' => $dados['nome'],
            ':descricao' => $dados['descricao'],
            ':id' => $id
        ]);
    }

    public function excluir($id)
    {
        $stmt = $this->db->prepare('DELETE FROM categorias WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
