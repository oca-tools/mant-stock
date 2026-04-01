<?php
// Modelo de inventarios
class Inventario extends ModeloBase
{
    public function listar($limite = 50)
    {
        $stmt = $this->db->prepare('SELECT i.*, p.nome AS produto_nome, u.nome AS usuario_nome FROM inventarios i LEFT JOIN produtos p ON p.id = i.produto_id LEFT JOIN usuarios u ON u.id = i.usuario_id ORDER BY i.data_inventario DESC LIMIT :limite');
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare('INSERT INTO inventarios (produto_id, quantidade_sistema, quantidade_real, diferenca, usuario_id, motivo_ajuste, data_inventario) VALUES (:produto_id, :quantidade_sistema, :quantidade_real, :diferenca, :usuario_id, :motivo_ajuste, NOW())');
        $stmt->execute([
            ':produto_id' => $dados['produto_id'],
            ':quantidade_sistema' => $dados['quantidade_sistema'],
            ':quantidade_real' => $dados['quantidade_real'],
            ':diferenca' => $dados['diferenca'],
            ':usuario_id' => $dados['usuario_id'],
            ':motivo_ajuste' => $dados['motivo_ajuste']
        ]);
        return $this->db->lastInsertId();
    }
}
